<?php
$page_title = 'Card Explorer';
require_once 'init.php';
require_once 'header.php';
?>

<!-- EXPLORER TOP BAR -->
<section id="explorerControls" class="stat-card explorer-top-bar" style="margin-bottom: 2rem; padding: 20px;">
    <div style="display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap;">
        <div class="admin-form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
            <label>Search Collection</label>
            <input type="text" id="cardSearch" placeholder="Type name to filter..." class="admin-input" onkeyup="filterCards()" style="padding: 12px 18px;">
        </div>
        <div class="admin-form-group" style="width: 200px; margin-bottom: 0;">
            <label>Rarity Tier</label>
            <select id="rarityFilter" class="admin-input" onchange="filterCards()" style="padding: 12px 18px;">
                <option value="">All Rarities</option>
                <option value="Legendary">Legendary</option>
                <option value="Epic">Epic</option>
                <option value="Rare">Rare</option>
                <option value="Common">Common</option>
            </select>
        </div>
        <div class="sys-item" style="padding: 8px 15px; background: rgba(0,0,0,0.3); border-radius: 10px; display: flex; align-items: center; gap: 10px; height: 50px;">
            <div class="sys-label" style="font-size: 0.65rem; text-transform: uppercase; color: var(--text-muted);">Visible</div>
            <div class="sys-value" id="visibleCardCount" style="font-size: 1.1rem; color: #00e5ff; font-weight: 800; font-family: 'Outfit';">0</div>
        </div>
        <div class="admin-form-group" style="width: 280px; margin-bottom: 0; position: relative;">
            <label>Verify Card SN</label>
            <div style="display: flex; gap: 5px;">
                <input type="text" id="snDecryptInput" placeholder="Enter #SN..." class="admin-input" style="padding: 10px 15px; font-size: 0.75rem; font-family: 'Outfit'; font-weight: 800; border-color: rgba(255,255,255,0.1);">
                <button onclick="unscrambleSNTool()" class="admin-submit" style="width: auto; padding: 0 15px; font-size: 0.6rem; background: var(--accent-secondary); color: black;">CHECK</button>
            </div>
        </div>
    </div>
</section>

<!-- MAIN EXPLORER GRID -->
<section id="cardExplorerGrid" class="card-explorer">
    <div class="card-grid" id="mainCardGrid">
        <?php for($i=0; $i<12; $i++): ?>
            <div class="card-entry skeleton" style="height: 380px; border: none;"></div>
        <?php endfor; ?>
    </div>
</section>


<script>
    window.addEventListener('load', () => hydrateCards());
    
    /**
     * compressSNS() - No longer strictly used for ranges now that we scramble, 
     * but kept as a helper for mapping the result.
     */
    function compressSNS(sns) {
        if (!sns || sns.length === 0) return [];
        sns = sns.map(Number).sort((a, b) => a - b);
        let ranges = [];
        let start = sns[0];
        let end = sns[0];
        for (let i = 1; i <= sns.length; i++) {
            if (i < sns.length && sns[i] === end + 1) {
                end = sns[i];
            } else {
                ranges.push(start === end ? `#${start}` : `#${start}-${end}`);
                if (i < sns.length) {
                    start = sns[i];
                    end = sns[i];
                }
            }
        }
        return ranges;
    }

    async function unscrambleSNTool() {
        const input = document.getElementById('snDecryptInput').value.replace('#', '').trim();
        if (!input) return;

        const fd = new FormData();
        fd.append('ajax_action', 'unscramble_sn');
        fd.append('code', input);

        try {
            const res = await secureFetch('api/admin.php', { method: 'POST', body: fd });
            if (res.success) {
                Swal.fire({
                    title: 'Card SN Information',
                    html: `
                        <div style="text-align: left; background: rgba(0,0,0,0.2); padding: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); font-family: 'Inter', sans-serif;">
                            <div style="margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 10px;">
                                <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">Card SN</div>
                                <div style="font-size: 1.5rem; color: #fff; font-weight: 900; font-family: 'Outfit';">#${res.db_id}</div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                <div>
                                    <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Current Owner</div>
                                    <div style="font-size: 0.9rem; color: #00e5ff; font-weight: 700;">${res.owner}</div>
                                    <div style="font-size: 0.6rem; color: rgba(255,255,255,0.3); font-family: monospace;">${res.owner_id}</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Card Name</div>
                                    <div style="font-size: 0.9rem; color: #fff; font-weight: 700;">${res.card_name.replace(/_/g, ' ')}</div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                <div>
                                    <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Rarity</div>
                                    <div style="font-size: 0.9rem; color: var(--accent-secondary); font-weight: 700;">${res.rarity}</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Tier</div>
                                    <div style="font-size: 0.9rem; color: #fff; font-weight: 700;">${res.tier}</div>
                                </div>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Variation</div>
                                <div class="sn-tag-container" style="display: inline-flex; padding: 0;">
                                    <span class="sn-chip" style="margin: 0; padding: 4px 12px; font-size: 0.75rem;">
                                        ${res.variant ? res.variant.toUpperCase() : 'STANDARD'}
                                    </span>
                                </div>
                            </div>

                            <div style="background: rgba(0,0,0,0.3); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.03);">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase;">Acquired On</div>
                                    <div style="font-size: 0.75rem; color: #fff; font-weight: 600;">${res.acquired}</div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 5px;">
                                    <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase;">Acquisition Source</div>
                                    <div style="font-size: 0.75rem; color: #00ffca; font-weight: 800; text-transform: uppercase;">${res.source}</div>
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    background: '#0c0a15',
                    color: '#fff',
                    confirmButtonColor: 'var(--accent-secondary)',
                    confirmButtonText: 'Close'
                });
            } else {
                Swal.fire({ title: 'Not Found', text: res.error, icon: 'error' });
            }
        } catch (e) {
            Swal.fire({ title: 'Error', text: e.message, icon: 'error' });
        }
    }

    async function hydrateCards() {
        try {
            const data = await secureFetch('api/admin.php?action=hydrate_admin');
            const grid = document.getElementById('mainCardGrid');
            
            // 1. DYNAMIC RARITY FILTER
            const rarityNames = [...new Set(data.cards.map(c => c.rarity_name))].sort();
            window.CACHED_RARITIES = rarityNames; // Store for modal
            
            // Populate filter dropdown
            const filter = document.getElementById('rarityFilter');
            const currentFilter = filter.value;
            filter.innerHTML = '<option value="">All Rarities</option>' + 
                rarityNames.map(r => `<option value="${r}" ${r === currentFilter ? 'selected' : ''}>${r}</option>`).join('');

            if (data.cards.length === 0) {
                grid.innerHTML = `
                    <div class="no-results-state">
                        <div class="no-results-icon">📭</div>
                        <div class="no-results-text">The card repository is currently empty.</div>
                    </div>
                `;
                document.getElementById('visibleCardCount').innerText = '0';
                return;
            }
            
            // 2. ODDS CALCULATION
            const unique_tiers = [...new Set(data.cards.map(c => c.rarity_tier))];
            const weight_sum = unique_tiers.reduce((a, b) => a + parseFloat(b), 0);
            
            grid.innerHTML = data.cards.map(card => {
                const tier_odds = (parseFloat(card.rarity_tier) / (weight_sum || 1)) * 100;
                const share = (data.stats.total_supply > 0) ? (card.current_supply / data.stats.total_supply) * 100 : 0;
                
                return `
                    <div class="card-entry ${card.rarity_name.toLowerCase().replace(/[^a-z0-9]/g, '-')}" 
                         data-id="${card.id}" data-name="${card.name}" data-rarity="${card.rarity_name}" 
                         data-tier="${card.rarity_tier}" data-file="${card.filename}" 
                         data-order="${card.card_order}" data-hidden="${card.is_hidden}" data-trade="${card.is_trade}">
                        <div class="card-visual">
                            <img src="${IMAGES_PATH}${card.filename}">
                            <div class="rarity-badge">${card.rarity_name}</div>
                            <div class="edit-btn" onclick="openEditModal(this.parentElement.parentElement)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </div>
                        </div>
                        <div class="card-details">
                            <h3>${card.name.replace(/_/g, ' ')}</h3>
                            <div class="meter-row">
                                <span>Supply: <strong>${card.current_supply}</strong></span>
                                <span>Owners: <strong>${card.owner_count}</strong></span>
                            </div>
                            <div class="stat-bars">
                                <div class="bar-group">
                                    <div class="bar-label">Theoretical Odds: ${tier_odds.toFixed(1)}%</div>
                                    <div class="bar-bg"><div class="bar-fill" style="width: ${Math.min(100, tier_odds)}%"></div></div>
                                </div>
                                <div class="bar-group">
                                    <div class="bar-label">Circulation Share: ${share.toFixed(2)}%</div>
                                    <div class="bar-bg secondary"><div class="bar-fill" style="width: ${Math.min(100, share * 10)}%"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            filterCards();
        } catch (e) { console.error(e); }
    }

    function filterCards() {
        const query = document.getElementById('cardSearch').value.toLowerCase();
        const rarity = document.getElementById('rarityFilter').value;
        const grid = document.getElementById('mainCardGrid');
        
        // Remove existing error message if present
        const existingMsg = grid.querySelector('.no-results-state');
        if (existingMsg) existingMsg.remove();

        let count = 0;
        document.querySelectorAll('.card-entry').forEach(card => {
            const cardName = card.dataset.name.toLowerCase().replace(/_/g, ' ');
            const searchVal = query.replace(/_/g, ' ');
            const matches = cardName.includes(searchVal) && (rarity === "" || card.dataset.rarity === rarity);
            card.style.display = matches ? 'block' : 'none';
            if (matches) count++;
        });

        if (count === 0 && document.querySelectorAll('.card-entry').length > 0) {
            const msg = document.createElement('div');
            msg.className = 'no-results-state';
            msg.innerHTML = `
                <div class="no-results-icon">🔍</div>
                <div class="no-results-text">No cards match "${query}" in the ${rarity || 'selected'} tier.</div>
            `;
            grid.appendChild(msg);
        }

        document.getElementById('visibleCardCount').innerText = count;
    }

    function openEditModal(el) {
        const d = el.dataset;
        const rarityOptions = (window.CACHED_RARITIES || []).map(r => 
            `<option value="${r}" ${r === d.rarity ? 'selected' : ''}>${r}</option>`
        ).join('');

        Swal.fire({
            title: 'Edit Card Details',
            width: '650px',
            background: '#161321',
            color: '#fff',
            showConfirmButton: false,
            showCloseButton: true,
            html: `
                <form id="editCardForm" style="text-align: left;">
                    <input type="hidden" name="admin_action" value="save_card">
                    <input type="hidden" name="c_id" value="${d.id}">
                    
                    <div class="admin-form-group">
                        <label>Card Name</label>
                        <input type="text" name="c_name" value="${d.name}" class="admin-input">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="admin-form-group">
                            <label>Rarity</label>
                            <select name="c_rarity" class="admin-input">${rarityOptions}</select>
                        </div>
                        <div class="admin-form-group">
                            <label>Weight</label>
                            <input type="text" name="c_tier" value="${d.tier}" class="admin-input">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="admin-form-group">
                            <label>Order</label>
                            <input type="number" name="c_order" value="${d.order}" class="admin-input">
                        </div>
                        <div style="display: flex; gap: 20px; padding-top: 25px;">
                            <div class="admin-form-group" style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="c_hidden" ${d.hidden == 1 ? 'checked' : ''}>
                                <label style="margin: 0;">Hidden</label>
                            </div>
                            <div class="admin-form-group" style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="c_trade" ${d.trade == 1 ? 'checked' : ''}>
                                <label style="margin: 0;">Tradeable</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-form-group">
                        <label>Filename</label>
                        <input type="text" name="c_file" value="${d.file}" class="admin-input">
                    </div>
                    
                    <button type="button" onclick="saveCard()" class="admin-submit" style="width: 100%;">Apply Changes</button>
                </form>

                <div class="owner-list-mgmt" style="margin-top: 30px; border-top: 1px solid var(--admin-border); padding-top: 20px; text-align: left;">
                    <h3 class="section-title">Ownership</h3>
                    <div id="ownerListContainer"></div>
                    <div class="add-owner-box" style="display: flex; gap: 10px; margin-top: 15px;">
                        <input type="text" id="new_owner_id" placeholder="Discord ID" class="admin-input">
                        <button type="button" onclick="addNewOwner('${d.id}')" class="admin-submit" style="width: auto; padding: 10px 22px; font-size: 0.65rem; white-space: nowrap;">Grant</button>
                    </div>
                </div>
            `,
            didOpen: () => {
                loadOwners(d.id);
            }
        });
    }

    async function loadOwners(cid) {
        try {
            const owners = await secureFetch(`api/admin.php?fetch_owners=${cid}`);
            const container = document.getElementById('ownerListContainer');
            
            if (owners.length === 0) {
                container.innerHTML = '<div style="color: var(--text-muted); padding: 20px; text-align: center;">No owners recorded for this card.</div>';
                return;
            }

            container.innerHTML = owners.map(o => `
                <div class="owner-row" id="row-${o.user_discord_id}" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; padding: 15px; background: rgba(12, 10, 21, 0.4); border: 1px solid var(--admin-border); border-radius: 12px; box-shadow: inset 0 0 10px rgba(0,0,0,0.2);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="${o.avatar}" class="tiny-avatar" style="width: 32px; height: 32px; border: 1px solid var(--glass-border); border-radius: 5px;">
                        <div style="flex-grow: 1; min-width: 0;">
                            <div style="font-size: 0.8rem; font-weight: 800; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: 0.5px;">${o.username}</div>
                            <div style="font-size: 0.6rem; color: var(--text-muted); font-family: 'Inter'; opacity: 0.5; letter-spacing: 1px;">ID: ${o.user_discord_id}</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="number" value="${o.count}" id="qty-${o.user_discord_id}" style="width: 50px; background: rgba(0,0,0,0.6); border: 1px solid rgba(0, 229, 255, 0.2); color: #00e5ff; border-radius: 5px; padding: 5px; font-size: 0.8rem; text-align: center; font-family: 'Outfit'; font-weight: 800;">
                            <button onclick="updateOwnerCount('${o.user_discord_id}', '${cid}')" class="owner-qty-btn count-up" style="padding: 5px 12px; font-size: 0.6rem; height: 32px; border-radius: 5px;">SYNC</button>
                            <button onclick="deleteOwner('${o.user_discord_id}', '${cid}')" class="owner-qty-btn count-down" style="width: 32px; height: 32px; font-size: 0.9rem; border-radius: 5px;">&times;</button>
                        </div>
                    </div>
                    <div class="sn-tag-container" style="display: flex; flex-wrap: wrap; gap: 6px; padding: 12px; background: rgba(0,0,0,0.3); border-radius: 8px; border: 1px solid rgba(255,255,255,0.03); position: relative;">
                        <span style="font-size: 0.5rem; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 950; letter-spacing: 2px; width: 100%; margin-bottom: 5px; display: flex; align-items:center; gap: 10px;">
                            SERIAL NUMBERS
                            <div style="flex-grow:1; height:1px; background:rgba(255,255,255,0.05);"></div>
                        </span>
                        ${(() => {
                            if (!o.sns || o.sns.length === 0) return '<span style="font-size: 10px; color: var(--text-muted); font-style: italic;">No instances found. Use SYNC to repair.</span>';
                            const limit = 20;
                            const shown = o.sns.slice(0, limit);
                            const more = o.sns.length - limit;
                            return shown.map(inst => {
                                const snVal = typeof inst === 'object' ? inst.sn : inst;
                                return `
                                    <span class="sn-chip" style="margin: 0;">
                                        #${snVal}
                                    </span>
                                `;
                            }).join('') + (more > 0 ? `<span style="font-size: 9px; color: var(--text-muted); align-self: center; margin-left: 5px;">+${more} more</span>` : '');
                        })()}
                    </div>
                </div>
            `).join('');
        } catch (e) { console.error("Load owners failed", e); }
    }

    async function updateOwnerCount(uid, cid) {
        const count = document.getElementById(`qty-${uid}`).value;
        const fd = new FormData();
        fd.append('ajax_action', 'update_owner_count');
        fd.append('user_id', uid);
        fd.append('card_id', cid);
        fd.append('count', count);
        
        try {
            const res = await secureFetch('api/admin.php', { method: 'POST', body: fd });
            if (res.success) {
                Swal.fire({ title: 'Quantity Updated', icon: 'success', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
            }
        } catch (e) { Swal.fire({ title: 'Error', text: e.message, icon: 'error' }); }
    }

    async function addNewOwner(cid) {
        const uid = document.getElementById('new_owner_id').value;
        const fd = new FormData();
        fd.append('admin_action', 'add_owner');
        fd.append('new_owner_id', uid);
        fd.append('c_id', cid);
        try {
            const res = await secureFetch('api/admin.php', { method: 'POST', body: fd });
            document.getElementById('new_owner_id').value = ''; 
            loadOwners(cid); 
        } catch (e) { Swal.fire({ title: 'Grant Failed', text: e.message, icon: 'error' }); }
    }

    async function deleteOwner(uid, cid) {
        const result = await Swal.fire({
            title: 'Remove Owner?',
            text: 'This will completely remove the record for this user.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Remove',
            cancelButtonColor: '#ff4e4e'
        });

        if (!result.isConfirmed) return;

        const fd = new FormData();
        fd.append('ajax_action', 'delete_owner');
        fd.append('user_id', uid);
        fd.append('card_id', cid);
        try {
            await secureFetch('api/admin.php', { method: 'POST', body: fd });
            loadOwners(cid);
        } catch (e) { Swal.fire({ title: 'Delete Failed', text: e.message, icon: 'error' }); }
    }

    async function saveCard() {
        const fd = new FormData(document.getElementById('editCardForm'));
        try {
            await secureFetch('api/admin.php', { method: 'POST', body: fd });
            Swal.fire({ title: 'Saved!', icon: 'success', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
            hydrateCards();
        } catch (e) { Swal.fire({ title: 'Save Failed', text: e.message, icon: 'error' }); }
    }
</script>

<?php require_once 'footer.php'; ?>
