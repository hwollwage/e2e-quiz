<x-layouts.dashboard title="Chat Member">
    @push('styles')
        <style>
            .admin-chat-wrap {
                display: grid;
                grid-template-columns: 280px 1fr;
                gap: 0;
                border: 1px solid var(--gym-border);
                height: calc(100vh - 140px);
                min-height: 520px;
            }
            @media (max-width: 768px) {
                .admin-chat-wrap { grid-template-columns: 1fr; height: auto; }
            }

            .ac-sidebar { border-right: 1px solid var(--gym-border); background: var(--gym-dark); display: flex; flex-direction: column; overflow: hidden; }
            .ac-sidebar-hd { padding: 16px; font-size: 0.7rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--gym-gray); border-bottom: 1px solid var(--gym-border); display: flex; align-items: center; justify-content: space-between; }
            .ac-search { padding: 10px 12px; background: #0d0d0d; border: none; border-bottom: 1px solid var(--gym-border); color: var(--gym-white); width: 100%; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; outline: none; }
            .ac-search::placeholder { color: var(--gym-gray); }
            .ac-contacts { flex: 1; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--gym-border) transparent; }
            .ac-contact { display: flex; align-items: center; gap: 10px; padding: 13px 16px; cursor: pointer; transition: background 0.2s; border-left: 2px solid transparent; position: relative; }
            .ac-contact.active { background: rgba(255,255,255,0.05); border-left-color: var(--gym-red); }
            .ac-contact:hover:not(.active) { background: rgba(255,255,255,0.02); }
            .ac-avatar { width: 36px; height: 36px; border-radius: 50%; background: rgba(232,41,42,0.18); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: var(--gym-red); flex-shrink: 0; }
            .ac-contact-info { flex: 1; min-width: 0; }
            .ac-contact-name { font-size: 0.85rem; font-weight: 600; color: var(--gym-white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .ac-contact-last { font-size: 0.72rem; color: var(--gym-gray); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
            .ac-badge { background: var(--gym-red); color: #fff; font-size: 0.62rem; font-weight: 700; padding: 2px 6px; border-radius: 999px; min-width: 18px; text-align: center; flex-shrink: 0; }
            .ac-empty { padding: 32px 16px; text-align: center; color: var(--gym-gray); font-size: 0.83rem; }

            /* Main chat area */
            .ac-main { display: flex; flex-direction: column; background: var(--gym-card); min-width: 0; }
            .ac-header { padding: 14px 20px; border-bottom: 1px solid var(--gym-border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
            .ac-header-name { font-size: 0.9rem; font-weight: 600; }
            .online-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #4ade80; margin-left: 6px; vertical-align: middle; }
            .ac-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 14px; scrollbar-width: thin; scrollbar-color: var(--gym-border) transparent; }
            .msg { display: flex; gap: 10px; max-width: 80%; }
            .msg.me { align-self: flex-end; flex-direction: row-reverse; }
            .msg-bubble { padding: 10px 14px; font-size: 0.83rem; line-height: 1.5; border: 1px solid var(--gym-border); background: #0d0d0d; color: var(--gym-white); word-break: break-word; }
            .msg.me .msg-bubble { background: rgba(232,41,42,0.15); border-color: rgba(232,41,42,0.3); }
            .msg-time { font-size: 0.68rem; color: var(--gym-gray); margin-top: 4px; text-align: right; }
            .msg-avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.68rem; font-weight: 700; flex-shrink: 0; align-self: flex-end; background: rgba(232,41,42,0.25); color: var(--gym-red); }
            .msg.me .msg-avatar { background: var(--gym-red); color: #fff; }

            .ac-input-row { display: flex; border-top: 1px solid var(--gym-border); flex-shrink: 0; }
            .ac-input { flex: 1; background: #0d0d0d; border: none; color: var(--gym-white); padding: 14px 18px; font-family: 'DM Sans', sans-serif; font-size: 0.88rem; outline: none; }
            .ac-input::placeholder { color: var(--gym-gray); }
            .ac-send { padding: 0 20px; background: var(--gym-red); border: none; color: #fff; cursor: pointer; font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; transition: background 0.2s; white-space: nowrap; }
            .ac-send:hover { background: #c0392b; }

            .ac-placeholder { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; gap: 12px; color: var(--gym-gray); }
            .ac-placeholder p { font-size: 0.83rem; }

            .msg-date-divider { text-align: center; font-size: 0.7rem; color: var(--gym-gray); letter-spacing: 0.08em; text-transform: uppercase; padding: 8px 0; }
        </style>
    @endpush

    <div class="admin-chat-wrap">
        <div class="ac-sidebar">
            <div class="ac-sidebar-hd">
                <span>Anggota</span>
                <span id="totalUnreadBadge" style="display:none;" class="ac-badge">0</span>
            </div>
            <input class="ac-search" type="text" placeholder="Cari anggota..."
                oninput="filterContacts(this.value)">
            <div class="ac-contacts" id="contactList">
                @forelse ($contacts as $c)
                    <div class="ac-contact" data-id="{{ $c['id'] }}" data-name="{{ $c['name'] }}"
                        data-initials="{{ $c['initials'] }}"
                        onclick="selectContact({{ $c['id'] }}, '{{ addslashes($c['name']) }}', '{{ $c['initials'] }}')">
                        <div class="ac-avatar">{{ $c['initials'] }}</div>
                        <div class="ac-contact-info">
                            <div class="ac-contact-name">{{ $c['name'] }}</div>
                            <div class="ac-contact-last">{{ $c['lastMsg'] ?? 'Belum ada pesan' }}</div>
                        </div>
                        @if ($c['unread'] > 0)
                            <span class="ac-badge" id="badge-{{ $c['id'] }}">{{ $c['unread'] }}</span>
                        @endif
                    </div>
                @empty
                    <div class="ac-empty" id="noContacts">
                        Belum ada anggota yang menghubungi.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="ac-main" id="acMain">
            <div class="ac-placeholder" id="acPlaceholder">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <p>Pilih anggota untuk memulai chat</p>
            </div>
            <div id="acChatArea" style="display:none;flex-direction:column;height:100%;">
                <div class="ac-header">
                    <span class="ac-header-name" id="acHeaderName">-</span>
                    <span style="font-size:0.72rem;color:var(--gym-gray)">Anggota</span>
                </div>
                <div class="ac-messages" id="acMessages"></div>
                <div class="ac-input-row">
                    <input class="ac-input" id="acInput" type="text"
                        placeholder="Tulis balasan..."
                        onkeydown="if(event.key==='Enter')sendReply()">
                    <button class="ac-send" onclick="sendReply()">Kirim</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const MY_ID       = {{ Auth::id() }};
            const MY_INITIALS = '{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}';
            const HISTORY_URL = '{{ route("chat.history") }}';
            const SEND_URL    = '{{ route("chat.send") }}';
            const CSRF        = '{{ csrf_token() }}';

            let activeContactId = null;
            let echoChannels    = {};

            function initAdminChatRealtime() {
                if (!window.Echo || typeof window.Echo.channel !== 'function') {
                    setTimeout(initAdminChatRealtime, 300);
                    return;
                }

                document.querySelectorAll('.ac-contact[data-id]').forEach(el => {
                    const memberId = parseInt(el.dataset.id);
                    subscribeChannel(memberId);
                });
            }

            initAdminChatRealtime();

            function subscribeChannel(memberId) {
                if (echoChannels[memberId]) return;
                const chMin = Math.min(MY_ID, memberId);
                const chMax = Math.max(MY_ID, memberId);
                echoChannels[memberId] = window.Echo.channel(`chat.${chMin}.${chMax}`)
                    .listen('.message.sent', (data) => {
                        if (data.sender_id === memberId && data.receiver_id === MY_ID) {
                            if (activeContactId === memberId) {
                                const time = new Date(data.created_at).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
                                appendMsg(data.message, 'them', time, data.sender_name);
                            } else {
                                incrementBadge(memberId);
                            }
                            updateLastMsg(memberId, data.message);
                        }
                    });
            }
            async function selectContact(id, name, initials) {
                activeContactId = id;
                document.querySelectorAll('.ac-contact').forEach(el => el.classList.remove('active'));
                const contactEl = document.querySelector(`.ac-contact[data-id="${id}"]`);
                if (contactEl) contactEl.classList.add('active');
                clearBadge(id);
                document.getElementById('acPlaceholder').style.display = 'none';
                const area = document.getElementById('acChatArea');
                area.style.display = 'flex';
                document.getElementById('acHeaderName').textContent = name;
                document.getElementById('acMessages').innerHTML =
                    '<div style="text-align:center;color:var(--gym-gray);font-size:.78rem;padding:20px;">Memuat pesan...</div>';
                try {
                    const res  = await fetch(`${HISTORY_URL}?with=${id}`);
                    const data = await res.json();
                    const wrap = document.getElementById('acMessages');
                    wrap.innerHTML = '';
                    if (!data.data.length) {
                        wrap.innerHTML = '<div style="text-align:center;color:var(--gym-gray);font-size:.78rem;padding:20px;">Belum ada pesan.</div>';
                        return;
                    }
                    let lastDate = null;
                    data.data.forEach(m => {
                        const msgDate = m.created_at ? new Date(m.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'}) : null;
                        if (msgDate && msgDate !== lastDate) {
                            wrap.innerHTML += `<div class="msg-date-divider">${msgDate}</div>`;
                            lastDate = msgDate;
                        }
                        const who  = m.sender_id === MY_ID ? 'me' : 'them';
                        const time = m.created_at ? new Date(m.created_at).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}) : '';
                        appendMsg(m.message, who, time, m.sender_name);
                    });
                } catch {
                    document.getElementById('acMessages').innerHTML =
                        '<div style="text-align:center;color:#f87171;font-size:.78rem;padding:20px;">Gagal memuat pesan.</div>';
                }
            }
            async function sendReply() {
                if (!activeContactId) return;
                const input = document.getElementById('acInput');
                const text  = input.value.trim();
                if (!text) return;
                input.value = '';
                appendMsg(text, 'me', timeNow(), MY_INITIALS);
                try {
                    await fetch(SEND_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({ receiver_id: activeContactId, message: text }),
                    });
                    updateLastMsg(activeContactId, text);
                } catch {
                    alert('Gagal mengirim pesan.');
                }
            }
            function appendMsg(text, who, time, senderName) {
                const wrap    = document.getElementById('acMessages');
                const isMe    = who === 'me';
                const initials = isMe
                    ? MY_INITIALS
                    : (senderName ? senderName.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase() : '??');
                wrap.innerHTML += `
                    <div class="msg ${isMe ? 'me' : ''}">
                        <div class="msg-avatar">${initials}</div>
                        <div>
                            <div class="msg-bubble">${escHtml(text)}</div>
                            <div class="msg-time">${time ?? ''}</div>
                        </div>
                    </div>`;
                wrap.scrollTop = wrap.scrollHeight;
            }
            function incrementBadge(memberId) {
                let badge = document.getElementById(`badge-${memberId}`);
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'ac-badge';
                    badge.id = `badge-${memberId}`;
                    badge.textContent = '1';
                    document.querySelector(`.ac-contact[data-id="${memberId}"]`)?.appendChild(badge);
                } else {
                    badge.textContent = parseInt(badge.textContent || 0) + 1;
                }
                updateTotalBadge();
            }

            function clearBadge(memberId) {
                document.getElementById(`badge-${memberId}`)?.remove();
                updateTotalBadge();
            }

            function updateTotalBadge() {
                const badges = document.querySelectorAll('.ac-badge:not(#totalUnreadBadge)');
                const total  = [...badges].reduce((s, b) => s + parseInt(b.textContent || 0), 0);
                const el     = document.getElementById('totalUnreadBadge');
                if (total > 0) { el.textContent = total; el.style.display = 'inline-block'; }
                else           { el.style.display = 'none'; }
            }

            function updateLastMsg(memberId, msg) {
                const el = document.querySelector(`.ac-contact[data-id="${memberId}"] .ac-contact-last`);
                if (el) el.textContent = msg.length > 40 ? msg.slice(0,40) + '…' : msg;
            }
            function filterContacts(q) {
                const term = q.toLowerCase();
                document.querySelectorAll('.ac-contact[data-id]').forEach(el => {
                    el.style.display = el.dataset.name.toLowerCase().includes(term) ? '' : 'none';
                });
            }
            function escHtml(s) {
                return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            }
            function timeNow() {
                return new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
            }
        </script>
    @endpush
</x-layouts.dashboard>
