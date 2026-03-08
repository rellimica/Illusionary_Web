<?php
$page_title = 'User Management';
require_once 'init.php';
require_once 'header.php';
?>

<div class="user-lookup-container">
    <div id="userSearchHero" class="user-search-hero">
        <h2 class="section-title">Locate User</h2>
        <div class="user-search-bar-unified">
            <div class="search-input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="userSearchId" placeholder="Enter Discord ID..." class="unified-input" onkeydown="if(event.key==='Enter') lookupUser()">
            </div>
            <button onclick="lookupUser()" class="unified-submit">Lookup User</button>
        </div>
    </div>

    <div id="userDetailsArea" class="user-details-hidden">
        <div class="placeholder-msg">
            <div class="radar-ping"></div>
            <p>Input a Discord ID to initiate collection retrieval.</p>
        </div>
    </div>
</div>

<script>
    let globalCards = [];

    window.addEventListener('load', async () => {
        // Load cards for the grant search
        const data = await secureFetch('api/admin.php?action=hydrate_admin');
        globalCards = data.cards;
    });

    async function lookupUser() {
        const uid = document.getElementById('userSearchId').value.trim();
        if (!uid) return;
        
        const area = document.getElementById('userDetailsArea');
        area.innerHTML = '<div class="placeholder-msg">Scanning database...</div>';
        area.classList.remove('user-details-hidden');

        try {
            const data = await secureFetch(`api/admin.php?action=lookup_user&uid=${uid}`);
            
            // Handle valid Discord ID but no data found (Unknown User is our API fallback)
            if (data.user.username === 'Unknown User') {
                area.innerHTML = `
                    <div class="no-results-state">
                        <div class="no-results-icon">❌</div>
                        <div class="no-results-text" style="color: #ff4e4e;">Discord ID "${uid}" could not be resolved.</div>
                        <p style="color: var(--text-muted); font-size: 0.8rem;">Ensure the Discord ID is correct and the user is a member of the tracked guilds.</p>
                    </div>
                `;
                return;
            }

            area.innerHTML = `
                <div class="user-profile-header">
                    <img src="${data.user.avatar}" class="profile-avatar">
                    <div class="profile-info" style="flex-grow: 1;">
                        <h2 class="profile-username" style="margin-bottom: 5px;">${data.user.username}</h2>
                        <code class="profile-id">${data.user.user_discord_id}</code>
                    </div>
                    <div class="mana-reserve-card" style="background: rgba(0,229,255,0.05); border: 1px solid rgba(0,229,255,0.2); border-radius: 16px; padding: 15px 25px; text-align: center; min-width: 140px;">
                        <div style="font-size: 0.65rem; color: var(--accent-secondary); text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px; margin-bottom: 5px;">Mana Reserve</div>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                            <button onclick="adjustMana('${data.user.user_discord_id}', -1)" class="owner-qty-btn count-down" style="width: 32px; height: 32px; font-size: 1.2rem;">-</button>
                            <span id="userManaDisp" style="font-size: 1.8rem; font-weight: 800; color: #fff; font-family: 'Outfit'; min-width: 40px;">${data.user.tokens}</span>
                            <button onclick="adjustMana('${data.user.user_discord_id}', 1)" class="owner-qty-btn count-up" style="width: 32px; height: 32px; font-size: 1.2rem;">+</button>
                        </div>
                    </div>
                </div>
                <div class="grant-section" style="margin-bottom: 40px; border-bottom: 1px solid var(--admin-border); padding-bottom: 30px;">
                    <h3 class="section-title">Grant New Card</h3>
                    <div style="display: flex; gap: 10px; position: relative;">
                        <div class="search-input-wrapper" style="flex-grow: 1; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px solid var(--admin-border); padding: 0 15px;">
                            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; opacity: 0.5;"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" id="cardLookupInput" placeholder="Search for a card to grant..." class="unified-input" onkeyup="searchGlobalCards(this.value)" style="padding: 12px 0;">
                        </div>
                        <div id="cardLookupResults" class="lookup-results-popover"></div>
                        <input type="hidden" id="selectedNewCardId">
                        <button onclick="grantSelectedCard('${data.user.user_discord_id}')" class="admin-submit" style="width: auto; padding: 0 25px;">GRANT CARD</button>
                    </div>
                </div>

                <h3 class="section-title">Collection (${data.collection.length} Unique Cards)</h3>
                
                ${data.collection.length === 0 ? `
                    <div class="no-results-state" style="padding: 40px 20px;">
                        <div class="no-results-icon" style="font-size: 2rem;">📦</div>
                        <div class="no-results-text">This user's vault is currently empty.</div>
                    </div>
                ` : `
                    <div class="user-coll-grid">
                        ${data.collection.map(c => `
                            <div class="mini-card rarity-${c.rarity_name.toLowerCase().replace(/[^a-z0-9]/g, '-')}">
                                <img src="${IMAGES_PATH}${c.filename}" class="mini-card-img">
                                <div class="mini-card-info">
                                    <div class="mini-card-name">${c.name.replace(/_/g, ' ')}</div>
                                    <div class="mini-card-count">Owned: ${c.count}</div>
                                    <div class="mini-card-sns" style="font-size: 0.55rem; color: var(--text-muted); padding-top: 4px; display: flex; flex-wrap: wrap; gap: 3px;">
                                        ${c.sns && c.sns.length > 0 ? compressSNS(c.sns).map(g => `<span class="sn-chip" style="padding: 1px 6px; border-radius: 4px; font-weight: 800; font-size: 9px; margin-bottom: 2px;">${g.label}</span>`).join('') : ''}
                                    </div>
                                </div>
                                <div class="mini-card-actions">
                                    <button onclick="quickUpdateOwner('${data.user.user_discord_id}', '${c.id}', ${parseInt(c.count) - 1})" class="owner-qty-btn count-down" style="width: 24px; height: 24px;">-</button>
                                    <button onclick="quickUpdateOwner('${data.user.user_discord_id}', '${c.id}', ${parseInt(c.count) + 1})" class="owner-qty-btn count-up" style="width: 24px; height: 24px;">+</button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `}
            `;
        } catch (e) {
            area.innerHTML = `<div class="no-results-state">
                <div class="no-results-icon">⚠️</div>
                <div class="no-results-text" style="color: #ff4e4e;">Lookup Malfunction: ${e.message}</div>
            </div>`;
        }
    }

    function searchGlobalCards(query) {
        const results = document.getElementById('cardLookupResults');
        if (!query || query.length < 2) { results.style.display = 'none'; return; }
        
        const searchVal = query.toLowerCase().replace(/_/g, ' ');
        const matches = globalCards.filter(c => {
            const cardName = c.name.toLowerCase().replace(/_/g, ' ');
            return cardName.includes(searchVal);
        }).slice(0, 5);
        
        if (matches.length === 0) { results.style.display = 'none'; return; }

        results.innerHTML = matches.map(c => `
            <div class="lookup-result-item rarity-${c.rarity_name.toLowerCase().replace(/[^a-z0-9]/g, '-')}" onclick="selectCardForGrant('${c.id}', '${c.name.replace(/_/g, ' ')}')">
                <img src="${IMAGES_PATH}${c.filename}" class="lookup-result-img">
                <div class="lookup-result-info">
                    <span class="lookup-result-name">${c.name.replace(/_/g, ' ')}</span>
                    <span class="lookup-result-rarity">${c.rarity_name}</span>
                </div>
            </div>
        `).join('');
        results.style.display = 'block';
    }

    function selectCardForGrant(id, name) {
        document.getElementById('selectedNewCardId').value = id;
        document.getElementById('cardLookupInput').value = name;
        document.getElementById('cardLookupResults').style.display = 'none';
    }

    async function grantSelectedCard(uid) {
        const cid = document.getElementById('selectedNewCardId').value;
        if (!cid) return;
        const fd = new FormData();
        fd.append('admin_action', 'add_owner');
        fd.append('new_owner_id', uid);
        fd.append('c_id', cid);
        try {
            await secureFetch('api/admin.php', { method: 'POST', body: fd });
            lookupUser(); 
            Swal.fire({ title: 'Success', icon: 'success', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false }); 
        } catch (e) { Swal.fire({ title: 'Grant Failed', text: e.message, icon: 'error' }); }
    }

    async function quickUpdateOwner(uid, cid, count) {
        if (count < 0) {
            const result = await Swal.fire({ title: 'Remove?', icon: 'warning', showCancelButton: true });
            if (!result.isConfirmed) return;
            const fd = new FormData();
            fd.append('ajax_action', 'delete_owner');
            fd.append('user_id', uid);
            fd.append('card_id', cid);
            const res = await secureFetch('api/admin.php', { method: 'POST', body: fd });
            if (res.success) lookupUser();
            return;
        }

        const fd = new FormData();
        fd.append('ajax_action', 'update_owner_count');
        fd.append('user_id', uid);
        fd.append('card_id', cid);
        fd.append('count', count);
        try {
            await secureFetch('api/admin.php', { method: 'POST', body: fd });
            lookupUser();
        } catch (e) { Swal.fire({ title: 'Update Failed', text: e.message, icon: 'error' }); }
    }
    async function adjustMana(uid, delta) {
        const disp = document.getElementById('userManaDisp');
        let current = parseInt(disp.innerText);
        let next = Math.max(0, current + delta);
        
        const fd = new FormData();
        fd.append('ajax_action', 'update_mana');
        fd.append('user_id', uid);
        fd.append('tokens', next);
        
        try {
            await secureFetch('api/admin.php', { method: 'POST', body: fd });
            disp.innerText = next;
        } catch (e) {
            Swal.fire({ title: 'Mana Update Failed', text: e.message, icon: 'error' });
        }
    }

    /**
     * compressSNS() - Groups consecutive SNs of the same variant.
     */
    function compressSNS(sns) {
        if (!sns || sns.length === 0) return [];
        // sns: [{sn: 123, variant: 'gold'}, ...]
        sns.sort((a, b) => a.sn - b.sn);
        
        let groups = [];
        let currentGroup = null;

        for (let inst of sns) {
            if (!currentGroup) {
                currentGroup = { start: inst.sn, end: inst.sn, variant: inst.variant };
            } else if (inst.sn === currentGroup.end + 1 && inst.variant === currentGroup.variant) {
                currentGroup.end = inst.sn;
            } else {
                groups.push(currentGroup);
                currentGroup = { start: inst.sn, end: inst.sn, variant: inst.variant };
            }
        }
        if (currentGroup) groups.push(currentGroup);

        return groups.map(g => {
            let label = g.start === g.end ? `#${g.start}` : `#${g.start}-${g.end}`;
            if (g.variant) label += ` (${g.variant})`;
            return { label, variant: g.variant };
        });
    }
</script>

<?php require_once 'footer.php'; ?>
