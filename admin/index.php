<?php
$page_title = 'Overview';
require_once 'init.php';
require_once 'header.php';
?>

<!-- SUMMARY CARDS -->
<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><span id="stat-card" class="skeleton" style="width: 60px; height: 40px; border-radius: 4px;"></span></div>
        <div class="stat-label">Card Size</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><span id="stat-supply" class="skeleton" style="width: 80px; height: 40px; border-radius: 4px;"></span></div>
        <div class="stat-label">Cards In Field</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><span id="stat-discovered" class="skeleton" style="width: 100px; height: 40px; border-radius: 4px;"></span></div>
        <div class="stat-label">Global Discovery</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><span id="stat-collectors" class="skeleton" style="width: 50px; height: 40px; border-radius: 4px;"></span></div>
        <div class="stat-label">Unique Collectors</div>
    </div>
</section>

<div class="admin-split-view">
    <div class="view-main">
        <section class="recent-section stat-card">
            <h2 class="section-title">Live Discovery Stream</h2>
            <div class="draw-list" id="discoveryStream">
                <?php for($i=0; $i<5; $i++): ?><div class="draw-item skeleton" style="height: 80px; border: none; margin-bottom: 10px;"></div><?php endfor; ?>
            </div>
        </section>
    </div>

    <div class="view-side">
        <section class="chart-section stat-card">
            <h2 class="section-title">Distribution</h2>
            <div class="chart-container"><canvas id="rarityChart"></canvas></div>
            <div id="rarityLegend" class="custom-legend" style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;"></div>
        </section>
    </div>
</div>

<!-- LEADERBOARDS ROW -->
<section class="leaderboards-row">
    <section class="stat-card leader-section">
        <h2 class="section-title">Top 10 Collectors</h2>
        <div class="leader-list" id="leaderboardList">
            <?php for($i=0; $i<5; $i++): ?><div class="leader-item skeleton" style="height: 60px; border: none; margin-bottom: 10px;"></div><?php endfor; ?>
        </div>
    </section>

    <section class="stat-card leader-section">
        <h2 class="section-title">
            Luckiest Discoverers
            <span class="info-trigger">?
                <span class="tooltip-box">Calculated based on the average rarity tier of all discovered cards (min. 3 cards). A higher index indicates a higher frequency of Legendary and Epic pulls.</span>
            </span>
        </h2>
        <div class="leader-list" id="luckList">
            <?php for($i=0; $i<5; $i++): ?><div class="leader-item skeleton" style="height: 60px; border: none; margin-bottom: 10px;"></div><?php endfor; ?>
        </div>
    </section>
</section>

<script>
    let rarityChart = null;

    window.addEventListener('load', () => {
        hydrateAdmin();
        // Polling for live updates every 15 seconds
        setInterval(hydrateAdmin, 15000);
    });

    async function hydrateAdmin() {
        try {
            const data = await secureFetch('api/admin.php?action=hydrate_admin');
            
            // 1. Stats
            document.getElementById('stat-card').innerText = data.stats.total_cards.toLocaleString();
            document.getElementById('stat-supply').innerText = data.stats.total_supply.toLocaleString();
            document.getElementById('stat-discovered').innerText = `${data.stats.global_uniques} / ${data.stats.total_cards}`;
            document.getElementById('stat-collectors').innerText = data.stats.unique_owners.toLocaleString();
            document.querySelectorAll('.skeleton').forEach(el => el.classList.remove('skeleton'));

            // 2. Chart
            updateRarityChart(data.rarity_dist);

            // 3. Activity Feed
            const stream = document.getElementById('discoveryStream');
            stream.innerHTML = data.feed.map(draw => `
                <div class="draw-item">
                    <div class="draw-img-wrapper"><img src="${IMAGES_PATH}${draw.filename}"></div>
                    <div class="draw-info">
                        <div class="draw-user-pill">
                            <img src="${draw.avatar}" class="tiny-avatar">
                            <div class="user-info-stack">
                                <div class="draw-username">${draw.username}</div>
                                <div class="draw-user-id">${draw.user_discord_id}</div>
                                <div class="draw-name">${draw.card_name.replace(/_/g, ' ')}</div>
                                <div class="draw-time">${draw.rarity_name} • <span class="local-time" data-ts="${draw.unix_ts}">...</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            // 4. Leaderboard
            document.getElementById('leaderboardList').innerHTML = data.leaderboard.map((p, i) => `
                <div class="leader-item rank-${i+1}">
                    <div class="leader-rank">#${i+1}</div>
                    <img src="${p.avatar}" class="tiny-avatar">
                    <div class="user-info-stack">
                        <div class="leader-id">${p.username}</div>
                        <div class="leader-count">${parseInt(p.total_cards).toLocaleString()} <span class="muted">(${p.unique_cards} unique)</span></div>
                    </div>
                </div>
            `).join('');

            // 5. Luckiest
            document.getElementById('luckList').innerHTML = data.luckiest.map((p, i) => `
                <div class="leader-item luck-item rank-${i+1}">
                    <div class="leader-rank">#${i+1}</div>
                    <img src="${p.avatar}" class="tiny-avatar">
                    <div class="user-info-stack">
                        <div class="leader-id">${p.username}</div>
                        <div class="leader-count">${(Math.round(100 / (p.luck_score || 1) * 10) / 10)} <span class="muted">index</span></div>
                    </div>
                </div>
            `).join('');

            updateLocalTimes();

        } catch (e) { console.error("Hydration failed", e); }
    }

    function updateRarityChart(dist) {
        const ctx = document.getElementById('rarityChart').getContext('2d');
        const labels = dist.map(d => d.rarity_name);
        const counts = dist.map(d => parseInt(d.total));
        const colors = labels.map(l => ({ 'Legendary': '#ff8000', 'Epic': '#a335ee', 'Rare': '#4e7cfe', 'Common': '#b0b0b0' }[l] || '#666'));

        if (!rarityChart) {
            rarityChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: labels, datasets: [{ data: counts, backgroundColor: colors, borderWidth: 0 }] },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: true } },
                    cutout: '70%',
                }
            });
        } else {
            rarityChart.data.labels = labels;
            rarityChart.data.datasets[0].data = counts;
            rarityChart.data.datasets[0].backgroundColor = colors;
            rarityChart.update();
        }

        // Generate Custom Legend
        const total = counts.reduce((a, b) => a + b, 0);
        document.getElementById('rarityLegend').innerHTML = labels.map((l, i) => {
            const perc = total > 0 ? Math.round((counts[i] / total) * 100) : 0;
            return `
                <div class="legend-item" style="display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.02); padding: 8px 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: ${colors[i]}; box-shadow: 0 0 10px ${colors[i]}80;"></div>
                    <div style="flex-grow: 1;">
                        <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; font-weight: 800;">${l}</div>
                        <div style="font-size: 0.85rem; font-weight: 700; color: #fff;">${counts[i].toLocaleString()} <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 400;">(${perc}%)</span></div>
                    </div>
                </div>
            `;
        }).join('');
    }
</script>

<?php require_once 'footer.php'; ?>
