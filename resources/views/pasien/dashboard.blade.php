@extends('layouts.app')

@section('title', 'MedicareSystem - Dashboard Pasien')

@section('content')

    <!-- ==================== HOME TAB ==================== -->
    @if($page === 'home')
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-user-md"></i></div>
                <div>
                    <h3>{{ $total_dokter }}</h3>
                    <p>Dokter Spesialis</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-ticket-alt"></i></div>
                <div>
                    <h3>{{ $total_antrean }}</h3>
                    <p>Antrean Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-comments"></i></div>
                <div>
                    <h3>{{ $total_chat }}</h3>
                    <p>Sesi Konsultasi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-notes-medical"></i></div>
                <div>
                    <h3>Digital</h3>
                    <p>Rekam Medis</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-user-md"></i> Konsultasi Dokter Spesialis</h2>
            <div class="doctor-list">
                @forelse($dokter_list as $dok)
                    <div class="doctor-item">
                        <div class="doctor-info">
                            <h4><i class="fas fa-stethoscope"></i> {{ $dok->name }}</h4>
                            <div style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                <span class="star-display">
                                    <i class="fas fa-star" style="color: #f59e0b;"></i>
                                    <strong>{{ number_format($dok->average_rating, 1) }}</strong>
                                </span>
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">({{ $dok->review_count }} Ulasan)</span>
                            </div>
                            <p style="margin-bottom: 4px;">{{ $dok->spesialis }}</p>
                            <div>
                                @if($dok->status_dokter === 'online')
                                    <span class="status-badge status-selesai" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 0.7rem; cursor: default;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background: #22c55e; border-radius: 50%;"></span>
                                        Online & Aktif
                                    </span>
                                @elseif($dok->status_dokter === 'sibuk')
                                    <span class="status-badge status-menunggu" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 0.7rem; cursor: default;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background: #eab308; border-radius: 50%;"></span>
                                        Sedang Sibuk
                                    </span>
                                @else
                                    <span class="status-badge status-batal" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 0.7rem; cursor: default;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background: #ef4444; border-radius: 50%;"></span>
                                        Offline
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($dok->status_dokter === 'online')
                            <a href="/pasien/chat/{{ $dok->id }}" class="btn btn-sm"><i class="fas fa-comment-dots"></i> Chat Sekarang</a>
                        @elseif($dok->status_dokter === 'sibuk')
                            <a href="/pasien/chat/{{ $dok->id }}" class="btn btn-sm btn-warning"><i class="fas fa-comment-dots"></i> Chat (Sibuk)</a>
                        @else
                            <span class="btn btn-sm" style="background:#e2e8f0; color:#94a3b8; cursor:not-allowed; box-shadow:none;"><i class="fas fa-ban"></i> Offline</span>
                        @endif
                    </div>
                @empty
                    <p>Tidak ada data dokter saat ini.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ==================== JADWAL TAB ==================== -->
    @if($page === 'jadwal')
        <div class="card">
            <h2><i class="fas fa-calendar-alt"></i> Kalender Jadwal Dokter</h2>
            <div style="margin-top: 15px;">
                <div id="calendar"></div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-list"></i> Daftar Lengkap Jadwal Dokter</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Dokter</th>
                            <th>Spesialis</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Ruangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $row)
                            <tr>
                                <td><strong>{{ $row->doctor_name }}</strong></td>
                                <td>{{ $row->spesialis }}</td>
                                <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                                <td>{{ $row->start_time }} - {{ $row->end_time }}</td>
                                <td>{{ $row->ruangan ?? '-' }}</td>
                                <td>
                                    <a href="/pasien/chat/{{ $row->doctor_id }}" class="btn btn-sm"><i class="fas fa-comment-dots"></i> Chat</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;">Tidak ada jadwal praktik terdekat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== CHAT TAB ==================== -->
    @if($page === 'chat')
        <div class="card">
            <h2><i class="fas fa-comments"></i> Konsultasi Chat</h2>
            
            @if(!$selected_dokter)
                <!-- Riwayat Sesi Konsultasi Chat -->
                <h3 style="margin-bottom: 15px;"><i class="fas fa-history"></i> Riwayat Konsultasi Chat Anda</h3>
                
                <div class="doctor-list" style="margin-top: 10px; margin-bottom: 40px;">
                    @forelse($chat_history as $session)
                        <div class="doctor-item">
                            <div class="doctor-info">
                                <h4><i class="fas fa-user-md"></i> dr. {{ $session->dokter_name }}</h4>
                                <p style="margin: 4px 0; font-size: 0.85rem; color: var(--text-muted);">
                                    Spesialisasi: {{ optional($session->dokter)->spesialis ?? 'Dokter' }}
                                </p>
                                <p style="margin: 4px 0; font-size: 0.8rem; color: var(--text-muted);">
                                    Terakhir diubah: {{ $session->updated_at->format('d M Y H:i') }} WIB
                                </p>
                                <div style="margin-top: 8px;">
                                    @if($session->status === 'aktif')
                                        <span class="status-badge status-selesai" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 0.7rem; cursor: default; background: #dcfce7; color: #15803d; border-radius: 9999px;">
                                            <span style="display: inline-block; width: 6px; height: 6px; background: #22c55e; border-radius: 50%;"></span>
                                            Sesi Aktif
                                        </span>
                                    @else
                                        <span class="status-badge status-batal" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 0.7rem; cursor: default; background: #f1f5f9; color: #475569; border-radius: 9999px;">
                                            <span style="display: inline-block; width: 6px; height: 6px; background: #94a3b8; border-radius: 50%;"></span>
                                            Selesai Konsultasi
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <a href="/pasien/chat/{{ $session->dokter_id }}" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 6px; background: #2b9e6e !important; color: white !important; font-weight: 700; border: none; opacity: 1;">
                                    <i class="fas fa-comment-dots"></i> Lanjutkan Chat
                                </a>
                                
                                <form action="/pasien/chat/session/{{ $session->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh riwayat sesi konsultasi chat dengan dr. {{ $session->dokter_name }} ini?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white; border: none; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 700;">
                                        <i class="fas fa-trash-alt"></i> Hapus Sesi
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 30px; color: var(--text-muted); background: #f8fafc; border-radius: 16px; border: 1px dashed #e2e8f0; width: 100%;">
                            <i class="fas fa-comments" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                            Belum ada riwayat sesi konsultasi chat saat ini.
                        </div>
                    @endforelse
                </div>

                <p style="margin-bottom: 20px; color: var(--text-muted); border-top: 1px solid #e2e8f0; padding-top: 30px;"><i class="fas fa-stethoscope"></i> Pilih dokter spesialis untuk memulai sesi konsultasi baru:</p>
                <div class="doctor-list">
                    @forelse($dokter_list as $doc)
                        <div class="doctor-item">
                            <div class="doctor-info">
                                <h4><i class="fas fa-stethoscope"></i> {{ $doc->name }}</h4>
                                <div style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <span class="star-display">
                                        <i class="fas fa-star" style="color: #f59e0b;"></i>
                                        <strong>{{ number_format($doc->average_rating, 1) }}</strong>
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">({{ $doc->review_count }} Ulasan)</span>
                                </div>
                                <p style="margin-bottom: 4px;">{{ $doc->spesialis }}</p>
                                <div>
                                    @if($doc->status_dokter === 'online')
                                        <span class="status-badge status-selesai" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 0.7rem; cursor: default;">
                                            <span style="display: inline-block; width: 6px; height: 6px; background: #22c55e; border-radius: 50%;"></span>
                                            Online & Aktif
                                        </span>
                                    @elseif($doc->status_dokter === 'sibuk')
                                        <span class="status-badge status-menunggu" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 0.7rem; cursor: default;">
                                            <span style="display: inline-block; width: 6px; height: 6px; background: #eab308; border-radius: 50%;"></span>
                                            Sedang Sibuk
                                        </span>
                                    @else
                                        <span class="status-badge status-batal" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 0.7rem; cursor: default;">
                                            <span style="display: inline-block; width: 6px; height: 6px; background: #ef4444; border-radius: 50%;"></span>
                                            Offline
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($doc->status_dokter === 'online')
                                <a href="/pasien/chat/{{ $doc->id }}" class="btn btn-sm" style="background: #2b9e6e !important; color: white !important; font-weight: 700;"><i class="fas fa-comment-dots"></i> Chat</a>
                            @elseif($doc->status_dokter === 'sibuk')
                                <a href="/pasien/chat/{{ $doc->id }}" class="btn btn-sm btn-warning" style="font-weight: 700;"><i class="fas fa-comment-dots"></i> Chat (Sibuk)</a>
                            @else
                                <span class="btn btn-sm" style="background:#e2e8f0; color:#94a3b8; cursor:not-allowed; box-shadow:none;"><i class="fas fa-ban"></i> Offline</span>
                            @endif
                        </div>
                    @empty
                        <p>Tidak ada dokter spesialis yang tersedia.</p>
                    @endforelse
                </div>
            @else
                <div style="margin-bottom: 15px;">
                    <a href="/pasien/chat" class="btn btn-sm btn-warning"><i class="fas fa-arrow-left"></i> Kembali ke List Dokter</a>
                </div>
                <h3>
                    <i class="fas fa-user-md"></i> Chat dengan dr. {{ $selected_dokter->name }} ({{ $selected_dokter->spesialis }})
                    @if($selected_dokter->status_dokter === 'online')
                        <span class="status-badge status-selelesai" style="font-size: 0.7rem; vertical-align: middle; margin-left: 8px; text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; cursor: default; background: #dcfce7; color: #15803d; border-radius: 9999px;">
                            <span style="display: inline-block; width: 6px; height: 6px; background: #22c55e; border-radius: 50%;"></span>
                            Online
                        </span>
                    @elseif($selected_dokter->status_dokter === 'sibuk')
                        <span class="status-badge status-menunggu" style="font-size: 0.7rem; vertical-align: middle; margin-left: 8px; text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; cursor: default; background: #fef3c7; color: #d97706; border-radius: 9999px;">
                            <span style="display: inline-block; width: 6px; height: 6px; background: #eab308; border-radius: 50%;"></span>
                            Sedang Sibuk
                        </span>
                    @else
                        <span class="status-badge status-batal" style="font-size: 0.7rem; vertical-align: middle; margin-left: 8px; text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; cursor: default; background: #fee2e2; color: #b91c1c; border-radius: 9999px;">
                            <span style="display: inline-block; width: 6px; height: 6px; background: #ef4444; border-radius: 50%;"></span>
                            Offline
                        </span>
                    @endif
                </h3>
                <div class="chat-container" style="margin-top: 15px;">
                    <div class="chat-messages" id="chatMessages">
                        @forelse($pesan_list as $msg)
                            <div class="chat-bubble {{ $msg->pengirim === 'pasien' ? 'chat-bubble-right' : 'chat-bubble-left' }}">
                                <strong>{{ $msg->pengirim_name }}</strong>
                                {!! nl2br(e($msg->pesan)) !!}
                                @if($msg->gambar)
                                    <div style="margin-top: 8px;">
                                        <a href="{{ asset('storage/' . $msg->gambar) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $msg->gambar) }}" class="chat-image" style="max-width:200px; border-radius:12px; display:block; box-shadow: var(--shadow-sm); cursor: pointer;">
                                        </a>
                                    </div>
                                @endif
                                <small>{{ date('H:i', strtotime($msg->waktu)) }}</small>
                            </div>
                        @empty
                            <p style="text-align:center; color:#94a3b8; padding: 40px 0;">✨ Belum ada pesan. Silakan kirim pesan keluhan Anda untuk memulai konsultasi!</p>
                        @endforelse
                    </div>

                    @if($konsultasi && $konsultasi->status === 'selesai' && !$konsultasi->is_rated)
                        <div class="rating-widget" style="padding: 24px; border-top: 1px solid var(--border-color); background: #f8fafc;">
                            <h4 style="color: var(--primary); text-align: center;"><i class="fas fa-star" style="color: #f59e0b;"></i> Berikan Penilaian Konsultasi</h4>
                            <p style="font-size: 0.85rem; color: var(--text-muted); text-align: center; margin-bottom: 15px;">
                                Konsultasi dengan <strong>dr. {{ $selected_dokter->name }}</strong> telah selesai. Umpan balik Anda sangat berarti bagi kami.
                            </p>
                            <form action="/pasien/chat/{{ $konsultasi->id }}/rate" method="POST" style="width: 100%; max-width: 400px; display: flex; flex-direction: column; gap: 15px; margin: 0 auto;">
                                @csrf
                                <div class="rating-stars">
                                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5" class="fas fa-star"></label>
                                    <input type="radio" id="star4" name="rating" value="4"><label for="star4" class="fas fa-star"></label>
                                    <input type="radio" id="star3" name="rating" value="3"><label for="star3" class="fas fa-star"></label>
                                    <input type="radio" id="star2" name="rating" value="2"><label for="star2" class="fas fa-star"></label>
                                    <input type="radio" id="star1" name="rating" value="1"><label for="star1" class="fas fa-star"></label>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <textarea name="ulasan" rows="3" placeholder="Tuliskan ulasan atau saran Anda untuk dokter ini (opsional)..." style="background: white; border-radius: var(--border-radius-md);"></textarea>
                                </div>
                                <button type="submit" class="btn" style="width: 100%; justify-content: center;"><i class="fas fa-paper-plane"></i> Kirim Penilaian</button>
                            </form>
                        </div>
                    @elseif($konsultasi && $konsultasi->status === 'selesai' && $konsultasi->is_rated)
                        <div style="padding: 20px; text-align: center; border-top: 1px solid var(--border-color); background: #f0fdf4; color: #166534;">
                            <p style="font-weight: 600; font-size: 0.9rem;"><i class="fas fa-check-circle"></i> Sesi konsultasi telah berakhir.</p>
                            <p style="font-size: 0.8rem; margin-top: 4px; opacity: 0.9;">Anda memberikan penilaian: 
                                <span class="star-display">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="fa{{ $i <= $konsultasi->rating ? 's' : 'r' }} fa-star"></i>
                                    @endfor
                                </span>
                            </p>
                            @if($konsultasi->ulasan)
                                <p style="font-size: 0.8rem; margin-top: 4px; font-style: italic; color: #15803d;">"{{ $konsultasi->ulasan }}"</p>
                            @endif
                            <div style="margin-top: 12px;">
                                <a href="/pasien/chat" class="btn btn-sm" style="background: #10b981; border: none; box-shadow: none;"><i class="fas fa-comments"></i> Konsultasi Baru</a>
                            </div>
                        </div>
                    @else
                        @if($selected_dokter->status_dokter === 'sibuk')
                            <div class="alert alert-warning" style="margin: 15px 20px 5px; border-radius: var(--border-radius-md); padding: 10px 16px; font-size: 0.85rem;">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Pemberitahuan:</strong> dr. {{ $selected_dokter->name }} sedang sibuk. Tanggapan terhadap pesan Anda mungkin akan lambat.
                            </div>
                        @elseif($selected_dokter->status_dokter === 'offline')
                            <div class="alert alert-error" style="margin: 15px 20px 5px; border-radius: var(--border-radius-md); padding: 10px 16px; font-size: 0.85rem;">
                                <i class="fas fa-times-circle"></i> <strong>Pemberitahuan:</strong> dr. {{ $selected_dokter->name }} sedang offline dan tidak melayani konsultasi saat ini.
                            </div>
                        @endif

                        @if($selected_dokter->status_dokter === 'offline')
                            <div class="chat-input" style="opacity: 0.6; cursor: not-allowed; display: flex; align-items: center; padding: 16px; background: #ffffff; border-top: 1px solid var(--border-color); gap: 12px;">
                                <input type="text" placeholder="Dokter sedang offline. Anda tidak dapat mengirim pesan saat ini..." disabled style="cursor: not-allowed; background: #cbd5e1; color: #64748b;">
                                <button type="button" class="btn" style="background:#cbd5e1; color:#94a3b8; cursor:not-allowed; box-shadow:none; border-radius: var(--border-radius-full);" disabled><i class="fas fa-paper-plane"></i> Kirim</button>
                            </div>
                        @else
                            <form action="/pasien/chat/send" method="POST" class="chat-input" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="dokter_id" value="{{ $selected_dokter->id }}">
                                <label for="gambar" class="btn btn-sm" style="background:#64748b; display:flex; align-items:center; justify-content:center; width:45px; height:45px; border-radius:50%; margin:0; box-shadow:none; cursor:pointer;"><i class="fas fa-paperclip"></i></label>
                                <input type="file" name="gambar" id="gambar" accept="image/*" style="display:none;" onchange="document.getElementById('fileName').innerHTML = this.files[0] ? this.files[0].name : '';">
                                <input type="text" name="pesan" placeholder="Ketik keluhan atau pesan Anda di sini..." autocomplete="off">
                                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim</button>
                            </form>
                            <div id="fileName" style="font-size: 0.75rem; color: var(--text-muted); padding: 5px 20px; font-weight: 600;"></div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- ==================== CHATBOT TAB ==================== -->
    @if($page === 'chatbot')
        <div class="card">
            <h2><i class="fas fa-robot"></i> Asisten Medicare</h2>
            <p style="color: var(--text-muted); margin-bottom: 20px;">
                Konsultasikan gejala yang Anda alami secara langsung dengan asisten pintar kami untuk dicarikan rekomendasi dokter spesialis yang tepat.
            </p>

            <div class="chatbot-container" style="border: 1px solid var(--border-color); border-radius: var(--border-radius-lg); overflow: hidden; background: white; box-shadow: var(--shadow-sm); display: flex; flex-direction: column;">
                <!-- Chatbox Messages -->
                <div class="chatbot-messages" id="chatbotMessages" style="height: 450px; overflow-y: auto; padding: 20px; background: #f8fafc; display: flex; flex-direction: column; gap: 16px;">
                    <!-- Bot message -->
                    <div class="chat-bubble chat-bubble-left" style="align-self: flex-start; max-width: 80%; display: flex; gap: 10px; border-radius: var(--border-radius-md); border-bottom-left-radius: 4px; border: 1px solid var(--border-color); padding: 16px;">
                        <div style="background: var(--accent-light); color: var(--accent); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <strong style="color: var(--primary); font-size: 0.85rem; display: block; margin-bottom: 4px;">Asisten Medicare</strong>
                            <p style="margin: 0; font-size: 0.9rem; line-height: 1.5; color: var(--text-main);">Halo! Saya Asisten Virtual Medicare. Apa keluhan atau gejala kesehatan yang sedang Anda rasakan saat ini? Saya akan membantu menganalisis keluhan Anda dan merekomendasikan dokter spesialis yang tepat.</p>
                            <small style="display: block; margin-top: 6px; font-size: 0.65rem; color: var(--text-muted);">Sekarang</small>
                        </div>
                    </div>
                </div>

                <!-- Chatbox Input -->
                <form id="chatbotForm" onsubmit="handleChatbotSubmit(event)" class="chat-input" style="display: flex; padding: 16px; background: #ffffff; border-top: 1px solid var(--border-color); gap: 12px; align-items: center;">
                    <input type="text" id="chatbotInput" placeholder="Ketik keluhan Anda di sini (misal: nyeri dada, anak demam, gigi linu)..." autocomplete="off" required style="flex: 1; background: #f1f5f9; border-radius: var(--border-radius-full); padding: 12px 20px; border: 2px solid var(--border-color);">
                    <button type="submit" class="btn" style="border-radius: var(--border-radius-full); padding: 12px 24px; display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0;"><i class="fas fa-paper-plane"></i> Kirim</button>
                </form>
            </div>
        </div>
    @endif

    <!-- ==================== REKAM MEDIS TAB ==================== -->
    @if($page === 'rekam')
        <div class="card">
            <h2><i class="fas fa-notes-medical"></i> Ajukan Rekam Medis (Konsultasi Gejala & Pemeriksaan Awal)</h2>
            <form action="/pasien/rekam-medis" method="POST">
                @csrf
                <div class="form-group">
                    <label for="keluhan">Keluhan Utama / Gejala yang Dirasakan</label>
                    <textarea name="keluhan" id="keluhan" rows="4" placeholder="Ceritakan keluhan fisik atau gejala yang Anda rasakan sedetail mungkin..." required></textarea>
                </div>
                
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(135px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="usia">Usia (Tahun)</label>
                        <input type="number" name="usia" id="usia" value="{{ $user->age }}" placeholder="Usia" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="tensi_darah">Tekanan Darah (mmHg)</label>
                        <input type="text" name="tensi_darah" id="tensi_darah" placeholder="Contoh: 120/80" pattern="^\d+\/\d+$" title="Format: Sistolik/Diastolik (e.g. 120/80)">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="suhu_tubuh">Suhu Tubuh (°C)</label>
                        <input type="number" name="suhu_tubuh" id="suhu_tubuh" step="0.1" placeholder="Contoh: 36.5" min="30" max="45">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="detak_jantung">Detak Jantung (bpm)</label>
                        <input type="number" name="detak_jantung" id="detak_jantung" placeholder="Contoh: 80" min="30" max="220">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="berat_badan">Berat Badan (kg)</label>
                        <input type="number" name="berat_badan" id="berat_badan" placeholder="Contoh: 65" min="1" max="300">
                    </div>
                </div>
                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim Rekam Medis</button>
            </form>
        </div>

        <div class="card">
            <h2><i class="fas fa-history"></i> Riwayat Diagnosa & Rekam Medis</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keluhan & Parameter Fisik</th>
                            <th>Kesimpulan Awal (Sistem)</th>
                            <th>Diagnosa Dokter</th>
                            <th>Resep Obat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($r->tanggal)) }}</td>
                                <td>
                                    <strong>Usia:</strong> {{ $r->usia ?: '-' }} Thn<br>
                                    <strong>Keluhan:</strong> {{ $r->keluhan }}<br>
                                    <div style="margin-top: 6px; font-size: 0.8rem; background: #f1f5f9; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0; display: inline-block;">
                                        🩺 Tensi: {{ $r->tensi_darah ?: '-' }} | 🌡️ Suhu: {{ $r->suhu_tubuh ? $r->suhu_tubuh . ' °C' : '-' }} | 💓 Nadi: {{ $r->detak_jantung ? $r->detak_jantung . ' bpm' : '-' }} | ⚖️ BB: {{ $r->berat_badan ? $r->berat_badan . ' kg' : '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: #475569; font-weight: 500; line-height: 1.4;">{{ $r->kesimpulan_awal ?: 'Data klinis tidak lengkap.' }}</span>
                                </td>
                                <td><strong>{{ $r->diagnosa ?: '-' }}</strong></td>
                                <td>
                                    {{ $r->resep ?: '-' }}
                                    @if($r->status === 'selesai' && $r->resep)
                                        <div style="margin-top: 5px;">
                                            <a href="/pasien/pesan-obat/{{ $r->id }}" class="btn btn-sm" style="padding: 4px 10px; font-size: 0.7rem;"><i class="fas fa-shopping-cart"></i> Pesan Obat</a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge {{ $r->status === 'selesai' ? 'status-selesai' : 'status-menunggu' }}">
                                        {{ $r->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;">Belum ada riwayat rekam medis terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== ANTREAN TAB ==================== -->
    @if($page === 'antrean')
        <div class="card">
            <h2><i class="fas fa-ticket-alt"></i> Antrean Saya</h2>
            <p style="color: var(--text-muted); margin-bottom: 20px;">
                Jika dokter mendiagnosa <strong>PARAH</strong>, jadwal janji temu dan nomor antrean otomatis akan diterbitkan di sini.
            </p>

            @forelse($antrean as $a)
                <div class="antrean-card" id="antrean-{{ $a->id }}">
                    <div class="grid-details">
                        <strong>Dokter Pemeriksa:</strong> <span>{{ $a->dokter_name }}</span>
                        <strong>Poliklinik Rujukan:</strong> <span style="font-weight: 700; color: var(--primary);">{{ $a->poli ?: 'Poli Umum' }}</span>
                        <strong>Tanggal Janji:</strong> <span>{{ date('d/m/Y', strtotime($a->tanggal)) }}</span>
                        <strong>Jam Praktik:</strong> <span>{{ $a->jam }} WIB</span>
                        <strong>Nomor Antrean:</strong> <span class="queue-no" style="font-size: 1.3rem; padding: 4px 12px; background: rgba(43, 158, 110, 0.1); color: #2b9e6e; border-radius: 8px; font-weight: 800;">{{ $a->nomor_antrean }}</span>
                        <strong>Status Tiket:</strong> 
                        <span class="status-badge {{ $a->status === 'menunggu' ? 'status-menunggu' : ($a->status === 'konfirmasi' ? 'status-konfirmasi' : ($a->status === 'selesai' ? 'status-selesai' : 'status-batal')) }}">
                            {{ $a->status }}
                        </span>
                    </div>

                    <!-- Print layout (hidden on screen by CSS mock but shown when printed) -->
                    <div id="print-area-{{ $a->id }}" class="print-area" style="display: none;">
                        <h3><i class="fas fa-notes-medical" style="color: #2b9e6e;"></i> MedicareSystem</h3>
                        <h4>KARTU ANTREAN JANJI TEMU</h4>
                        <hr>
                        <p><strong>Nomor Antrean:</strong> <span style="font-size: 1.4rem; font-weight:800; color:#2b9e6e;">{{ $a->nomor_antrean }}</span></p>
                        <p><strong>Nama Pasien:</strong> {{ $a->pasien_name }}</p>
                        <p><strong>Poliklinik Rujukan:</strong> {{ $a->poli ?: 'Poli Umum' }}</p>
                        <p><strong>Dokter Pemeriksa:</strong> {{ $a->dokter_name }}</p>
                        <p><strong>Jadwal Temu:</strong> {{ date('d/m/Y', strtotime($a->tanggal)) }} | {{ $a->jam }} WIB</p>
                        <p><strong>Keluhan:</strong> {{ $a->keluhan }}</p>
                        <hr>
                        <p style="font-size:0.8rem; font-weight: 500;">Silakan datang ke Poliklinik 15 menit sebelum jadwal pemeriksaan.</p>
                        <p style="font-size:0.65rem; color:#94a3b8; margin-top:10px;">© MedicareSystem Digital Health</p>
                    </div>

                    <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                        <button class="btn btn-sm" onclick="cetakAntrean({{ $a->id }})"><i class="fas fa-print"></i> Cetak Tiket</button>
                        @if($a->status === 'menunggu')
                            <a href="/pasien/antrean/{{ $a->id }}/batal" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')">
                                <i class="fas fa-times"></i> Batalkan Janji
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada antrean janji temu aktif.</p>
            @endforelse
        </div>
    @endif

    <!-- ==================== PESAN OBAT TAB ==================== -->
    @if($page === 'pesan_obat')
        <div class="card">
            <h2><i class="fas fa-shopping-cart"></i> Pemesanan Obat Resep</h2>
            <div style="background: #f8fafc; padding: 20px; border-radius: var(--border-radius-lg); margin-bottom: 24px; border: 1px solid var(--border-color);">
                <h4 style="color: var(--primary); margin-bottom: 10px;"><i class="fas fa-file-medical"></i> Rincian Resep Medis</h4>
                <p><strong>Tanggal Diagnosa:</strong> {{ date('d/m/Y', strtotime($rm->tanggal)) }}</p>
                <p><strong>Keluhan:</strong> {{ $rm->keluhan }}</p>
                <p><strong>Diagnosa Dokter:</strong> {{ $rm->diagnosa }}</p>
                <p style="margin-top: 10px; font-size: 1.1rem; color: var(--accent);"><strong>Resep Obat:</strong> {{ $rm->resep }}</p>
            </div>

            <form action="/pasien/pesan-obat" method="POST">
                @csrf
                <input type="hidden" name="rekam_medis_id" value="{{ $rm->id }}">
                
                <div class="form-group">
                    <label for="alamat_kirim">Alamat Pengiriman Lengkap</label>
                    <textarea name="alamat_kirim" id="alamat_kirim" rows="3" placeholder="Masukkan alamat pengiriman lengkap Anda (Jalan, RT/RW, Kecamatan, Kota, Kode Pos)..." required>{{ $user->alamat }}</textarea>
                </div>

                <div class="demo-info" style="margin-bottom: 20px;">
                    <i class="fas fa-info-circle" style="color: var(--accent);"></i> Setelah pesanan dibuat, silakan lakukan pembayaran. Admin akan memproses pengiriman obat ke alamat Anda.
                </div>

                <button type="submit" class="btn"><i class="fas fa-check-circle"></i> Buat Pesanan Obat</button>
                <a href="/pasien/rekam-medis" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            </form>
        </div>
    @endif

    <!-- ==================== DAFTAR PESANAN TAB ==================== -->
    @if($page === 'daftar_pesanan')
        <div class="card">
            <h2><i class="fas fa-pills"></i> Riwayat Pesanan Obat Saya</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Resep Obat</th>
                            <th>Alamat Kirim</th>
                            <th>Total Pembayaran</th>
                            <th>Status Pesanan</th>
                            <th>Aksi / Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesanan as $p)
                            <tr>
                                <td><strong>#ORD-{{ $p->id }}</strong></td>
                                <td>{{ date('d/m/Y H:i', strtotime($p->created_at)) }}</td>
                                <td><span style="font-weight: 600; color: var(--accent);">{{ $p->resep }}</span></td>
                                <td>{{ $p->alamat_kirim }}</td>
                                <td><strong>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong></td>
                                <td>
                                    @php
                                        $badgeClass = 'status-menunggu';
                                        if ($p->status === 'diproses') $badgeClass = 'status-konfirmasi';
                                        elseif ($p->status === 'dikirim') $badgeClass = 'status-konfirmasi';
                                        elseif ($p->status === 'selesai') $badgeClass = 'status-selesai';
                                        elseif ($p->status === 'dibatalkan') $badgeClass = 'status-batal';
                                    @endphp
                                    <span class="status-badge {{ $badgeClass }}">{{ str_replace('_', ' ', $p->status) }}</span>
                                    @if($p->status === 'menunggu_pembayaran' && $p->bukti_transfer)
                                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 4px; font-weight: 600;">
                                            <i class="fas fa-clock"></i> Menunggu Verifikasi
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($p->status === 'menunggu_pembayaran')
                                        @if(!$p->bukti_transfer)
                                            <button class="btn btn-sm" style="padding: 4px 10px; font-size: 0.7rem;" onclick="bukaModalBayar({{ $p->id }}, {{ $p->total_harga }})">
                                                <i class="fas fa-wallet"></i> Bayar Sekarang
                                            </button>
                                        @else
                                            <a href="{{ asset('storage/' . $p->bukti_transfer) }}" target="_blank" class="btn btn-sm btn-secondary" style="padding: 4px 10px; font-size: 0.7rem;">
                                                <i class="fas fa-image"></i> Lihat Bukti
                                            </a>
                                        @endif
                                    @elseif(in_array($p->status, ['diproses', 'dikirim', 'selesai']))
                                        <a href="/pasien/pesanan-obat/{{ $p->id }}/cetak-struk" target="_blank" class="btn btn-sm btn-success" style="padding: 4px 10px; font-size: 0.7rem; background: #10b981; border: none; box-shadow: none;">
                                            <i class="fas fa-print"></i> Cetak Struk
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center;">Belum ada riwayat pemesanan obat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAYMENT MODAL -->
        <div id="modalBayar" class="modal">
            <div class="modal-content" style="max-width: 500px;">
                <h3><i class="fas fa-file-invoice-dollar" style="color: var(--accent);"></i> Pembayaran Pesanan Obat</h3>
                <div style="margin-top: 15px; background: #f8fafc; padding: 15px; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); font-size: 0.9rem; line-height: 1.6;">
                    <p style="margin-bottom: 5px; color: var(--text-muted);">Silakan lakukan transfer sebesar:</p>
                    <p style="font-size: 1.4rem; font-weight: 800; color: var(--accent); margin-bottom: 15px;" id="bayarTotal">Rp 0</p>
                    <p style="font-weight: 600; margin-bottom: 6px;">Metode Pembayaran (Transfer Bank):</p>
                    <p style="display:flex; align-items:center; gap:8px; margin-bottom: 4px;"><i class="fas fa-university" style="color: var(--primary);"></i> Bank Mandiri: <strong>123-456-7890</strong> <span style="font-size: 0.75rem; color: var(--text-muted);">(a/n Medicare Digital)</span></p>
                    <p style="display:flex; align-items:center; gap:8px;"><i class="fas fa-university" style="color: var(--primary);"></i> Bank BCA: <strong>098-765-4321</strong> <span style="font-size: 0.75rem; color: var(--text-muted);">(a/n Medicare Digital)</span></p>
                </div>
                
                <form id="formBayar" action="" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                    @csrf
                    <div class="form-group">
                        <label for="bukti_transfer" style="font-weight: 600;">Unggah Foto Bukti Transfer</label>
                        <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/*" required style="padding: 10px; border-radius: 8px;">
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalBayar()">Batal</button>
                        <button type="submit" class="btn"><i class="fas fa-upload"></i> Kirim Bukti Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ==================== BANTUAN / CS TAB ==================== -->
    @if($page === 'bantuan')
        <style>
            /* FAQ Accordion Styling */
            .faq-container {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-top: 15px;
            }
            .faq-item {
                border: 1px solid var(--border-color);
                border-radius: var(--border-radius-md);
                background: #f8fafc;
                overflow: hidden;
                transition: var(--transition);
            }
            .faq-item:hover {
                border-color: rgba(43, 158, 110, 0.3);
                background: #ffffff;
            }
            .faq-header {
                padding: 14px 20px;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-weight: 600;
                font-size: 0.9rem;
                color: var(--primary);
                user-select: none;
            }
            .faq-header i {
                color: var(--accent);
                transition: transform 0.3s ease;
            }
            .faq-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease-out, padding 0.3s ease;
                padding: 0 20px;
                font-size: 0.85rem;
                line-height: 1.6;
                color: var(--text-muted);
                border-top: 1px solid transparent;
            }
            .faq-item.active {
                background: #ffffff;
                border-color: var(--accent);
                box-shadow: var(--shadow-sm);
            }
            .faq-item.active .faq-header i {
                transform: rotate(180deg);
            }
            .faq-item.active .faq-content {
                padding: 14px 20px;
                border-top-color: var(--border-color);
                max-height: 200px; /* reasonable max height for transition */
            }
        </style>

        <!-- Hubungi CS -->
        <div class="card" style="margin-bottom: 24px;">
            <h2><i class="fas fa-headset" style="color: var(--accent);"></i> Hubungi CS</h2>
            <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 0.9rem;">
                Mengalami kendala penggunaan Medicare atau butuh bantuan administrasi? Kirimkan pesan kendala Anda, CS Medicare siap membantu Anda.
            </p>
            
            <form action="/pasien/bantuan/kirim" method="POST">
                @csrf
                <div class="form-group">
                    <label for="pesan" style="font-weight: 600;">Jelaskan Kendala atau Pertanyaan Anda</label>
                    <textarea name="pesan" id="pesan" rows="4" placeholder="Tuliskan pesan bantuan Anda secara detail di sini..." required style="border-radius: var(--border-radius-md); border: 2px solid var(--border-color);"></textarea>
                </div>
                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim Pesan Bantuan</button>
            </form>
        </div>

        <!-- FAQ Layanan -->
        <div class="card">
            <h2><i class="fas fa-info-circle"></i> FAQ Layanan</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">Pertanyaan umum untuk bantuan cepat dan mandiri:</p>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-header">
                        <span>Bagaimana cara melakukan konsultasi online?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-content">
                        Buka menu <strong>"Konsultasi Chat"</strong>, pilih dokter spesialis aktif (status online/sibuk), lalu klik tombol Chat untuk memulai sesi konsultasi langsung dengan dokter.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-header">
                        <span>Kapan nomor antrean janji temu diterbitkan?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-content">
                        Jika hasil pemeriksaan chat online menunjukkan Anda memerlukan penanganan fisik langsung di klinik, nomor antrean Anda otomatis terbit di menu <strong>"Antrean Saya"</strong>.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-header">
                        <span>Bagaimana cara menebus resep obat?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-content">
                        Buka menu <strong>"Rekam Medis"</strong> pada dashboard Anda, klik tombol <strong>"Pesan Obat"</strong> di rekam medis terkait, lalu lakukan pembayaran via transfer bank dan unggah bukti transfer.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-header">
                        <span>Berapa lama respon Customer Service?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-content">
                        Tim CS kami aktif setiap hari dari jam 08:00 - 20:00 WIB. Pertanyaan Anda biasanya akan direspon kurang dari 1 jam.
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    @if($page === 'jadwal')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var events = @json($events ?? []);
                var calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth'
                        },
                        buttonText: {
                            today: 'Hari Ini',
                            month: 'Bulan'
                        },
                        events: events,
                        height: 'auto',
                        handleWindowResize: true,
                        eventClick: function(info) {
                            alert('🩺 Dokter Praktik: ' + info.event.title + '\n📅 Silakan buka menu chat untuk konsultasi langsung.');
                        }
                    });
                    calendar.render();
                }
            });
        </script>
    @endif

    @if($page === 'chat' && $selected_dokter)
        <script>
            // Scroll to bottom of chat
            document.addEventListener('DOMContentLoaded', function() {
                var chatMessages = document.getElementById('chatMessages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            });
        </script>
    @endif

    @if($page === 'antrean')
        <script>
            function cetakAntrean(id) {
                var printContents = document.getElementById('print-area-' + id).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                location.reload();
            }
        </script>
    @endif

    @if($page === 'daftar_pesanan')
        <script>
            function bukaModalBayar(id, total) {
                document.getElementById('formBayar').action = '/pasien/pesanan-obat/' + id + '/upload-bukti';
                document.getElementById('bayarTotal').innerHTML = 'Rp ' + total.toLocaleString('id-ID');
                document.getElementById('modalBayar').style.display = 'flex';
            }
            function tutupModalBayar() {
                document.getElementById('modalBayar').style.display = 'none';
            }
        </script>
    @endif

    @if($page === 'bantuan')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const faqHeaders = document.querySelectorAll('.faq-header');
                faqHeaders.forEach(header => {
                    header.addEventListener('click', () => {
                        const item = header.parentElement;
                        item.classList.toggle('active');
                        
                        // Close other FAQ items
                        const allItems = document.querySelectorAll('.faq-item');
                        allItems.forEach(i => {
                            if (i !== item) {
                                i.classList.remove('active');
                            }
                        });
                    });
                });
            });
        </script>
    @endif

    @if($page === 'chatbot')
        <script>
            const doctorsData = @json($dokter_list ?? []);
            
            const keywordMapping = [
                {
                    specialization: 'Jantung',
                    keywords: ['jantung', 'dada sesak', 'sesak dada', 'dada nyeri', 'nyeri dada', 'berdebar', 'palpitasi', 'stroke', 'pembuluh darah', 'koroner', 'angina', 'sakit dada', 'dada sakit', 'sesak nafas', 'napas pendek', 'lelah', 'hipertensi', 'darah tinggi']
                },
                {
                    specialization: 'Anak',
                    keywords: ['anak', 'bayi', 'balita', 'pediat', 'imunisasi anak', 'tumbuh kembang', 'anak demam', 'anak batuk', 'anak pilek', 'imunisasi', 'diare anak', 'muntah anak', 'kejang demam', 'bocah']
                },
                {
                    specialization: 'Mata',
                    keywords: ['mata', 'buram', 'kabur', 'katarak', 'belekan', 'minus', 'silinder', 'perih mata', 'mata merah', 'kacamata', 'penglihatan', 'silindris', 'rabun', 'buta', 'gatal mata']
                },
                {
                    specialization: 'Saraf',
                    keywords: ['saraf', 'pusing', 'vertigo', 'migrain', 'sakit kepala', 'kesemutan', 'lumpuh', 'baal', 'kejang', 'kebas', 'syaraf', 'otak', 'migren', 'cegat', 'nyeri sendi']
                },
                {
                    specialization: 'Gigi',
                    keywords: ['gigi', 'gusi', 'linu', 'ompong', 'behel', 'cabut gigi', 'gigi berlubang', 'gigi sakit', 'sakit gigi', 'karang gigi', 'scaling', 'mulut bau', 'sariawan', 'gusinya', 'gigi ngilu', 'gusi bengkak', 'tambal gigi', 'kawat gigi']
                },
                {
                    specialization: 'THT',
                    keywords: ['telinga', 'hidung', 'tenggorokan', 'amandel', 'sinus', 'budek', 'tuli', 'pilek menahun', 'serak', 'flu', 'batuk', 'tenggorokan sakit', 'budeg', 'pilek', 'sinusitis', 'radang tenggorokan', 'sakit tenggorokan', 'telinga berdenging', 'congek']
                },
                {
                    specialization: 'Kulit & Kelamin',
                    keywords: ['kulit', 'gatal', 'alergi kulit', 'jerawat', 'eksim', 'kurap', 'kudis', 'panu', 'jamur kulit', 'kelamin', 'ruam', 'bisul', 'koreng', 'alergi', 'kutil', 'rambut rontok', 'ketombe']
                },
                {
                    specialization: 'Bedah',
                    keywords: ['bedah', 'operasi', 'benjolan', 'usus buntu', 'luka bakar', 'patah tulang', 'hernia', 'tumor', 'kanker', 'jahit luka', 'kecelakaan', 'patah', 'kista']
                },
                {
                    specialization: 'Penyakit Dalam',
                    keywords: ['lambung', 'maag', 'diabetes', 'ginjal', 'tensi', 'hipertensi', 'organ hati', 'sakit liver', 'penyakit hati', 'lemas', 'demam', 'pusing', 'kolesterol', 'asam urat', 'flu', 'batuk', 'gula darah', 'kencing manis', 'gerd', 'perut kembung', 'mual', 'muntah', 'tifus', 'tipes', 'demam berdarah', 'dbd', 'paru-paru', 'asma']
                },
                {
                    specialization: 'Psikolog',
                    keywords: ['psikolog', 'mental', 'stres', 'stress', 'depresi', 'kecemasan', 'insomnia', 'trauma', 'bipolar', 'psikis', 'jiwa', 'sedih', 'cemas', 'overthink', 'konseling', 'konsultasi jiwa', 'kesehatan mental', 'panik', 'curhat', 'sakit hati', 'patah hati', 'galau', 'kesepian', 'kecewa', 'murung', 'cemas berlebih']
                }
            ];

            // Build dynamic keyword mapping for any specializations registered in the database
            const dbSpecializations = [...new Set(doctorsData.map(doc => doc.spesialis).filter(Boolean))];
            const activeKeywordMapping = [...keywordMapping];

            dbSpecializations.forEach(spec => {
                const exists = activeKeywordMapping.some(m => m.specialization.toLowerCase() === spec.toLowerCase());
                if (!exists) {
                    activeKeywordMapping.push({
                        specialization: spec,
                        keywords: [spec.toLowerCase(), `spesialis ${spec.toLowerCase()}`]
                    });
                }
            });

            // Keep track of fallback alert state, so we only append it once
            let isFallbackAlertAppended = false;

            function handleChatbotSubmit(event) {
                event.preventDefault();
                const inputEl = document.getElementById('chatbotInput');
                const messageText = inputEl.value.trim();
                if (!messageText) return;

                // 1. Render user message
                appendUserMessage(messageText);
                inputEl.value = '';

                // 2. Render typing indicator
                const typingId = appendTypingIndicator();

                // 3. Scroll to bottom
                scrollToBottom();

                // 4. Respond using the offline local keyword matcher (simulating response time)
                setTimeout(() => {
                    removeTypingIndicator(typingId);
                    generateBotResponseStandard(messageText);
                    scrollToBottom();
                }, 750);
            }

            function triggerFallback(typingId, messageText, reason) {
                if (!isFallbackAlertAppended) {
                    isFallbackAlertAppended = true;
                    appendFallbackAlert(reason);
                }
                generateBotResponseStandard(messageText);
            }

            function appendFallbackAlert(reason) {
                const messagesContainer = document.getElementById('chatbotMessages');
                const alertHtml = `
                    <div style="background: #fef3c7; border: 1px solid #f59e0b; color: #b45309; font-size: 0.75rem; padding: 10px 14px; border-radius: var(--border-radius-md); line-height: 1.4; display: flex; gap: 8px; align-items: flex-start; margin: 10px 0; width: 100%;">
                        <i class="fas fa-exclamation-triangle" style="margin-top: 2px;"></i>
                        <div>
                            <strong>Mode AI Belum Aktif:</strong> ${reason}<br>
                            <em>Beralih secara otomatis ke asisten pencocok kata kunci lokal (offline).</em>
                        </div>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', alertHtml);
            }

            function renderAiResponse(userText, messageContent, recommendedDoctorIds) {
                const messagesContainer = document.getElementById('chatbotMessages');
                let matchedDoctorsHtml = "";

                if (recommendedDoctorIds && recommendedDoctorIds.length > 0) {
                    const matchedDoctors = doctorsData.filter(doc => recommendedDoctorIds.includes(doc.id));
                    if (matchedDoctors.length > 0) {
                        matchedDoctorsHtml = `
                            <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 4px;">Rekomendasi Dokter Terkait:</p>
                                ${matchedDoctors.map(doc => renderDoctorCardHtml(doc)).join('')}
                            </div>
                        `;
                    }
                }

                const botResponseHtml = `
                    <div class="chat-bubble chat-bubble-left" style="align-self: flex-start; max-width: 80%; display: flex; gap: 10px; border-radius: var(--border-radius-md); border-bottom-left-radius: 4px; border: 1px solid var(--border-color); padding: 16px;">
                        <div style="background: var(--accent-light); color: var(--accent); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div style="flex: 1;">
                            <strong style="color: var(--primary); font-size: 0.85rem; display: block; margin-bottom: 4px;">Asisten Medicare (AI)</strong>
                            <div style="margin: 0; font-size: 0.9rem; line-height: 1.5; color: var(--text-main);">${messageContent}</div>
                            ${matchedDoctorsHtml}
                            <small style="display: block; margin-top: 8px; font-size: 0.65rem; color: var(--text-muted);">Sekarang</small>
                        </div>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', botResponseHtml);
            }

            function appendUserMessage(text) {
                const messagesContainer = document.getElementById('chatbotMessages');
                const userBubbleHtml = `
                    <div class="chat-bubble chat-bubble-right" style="align-self: flex-end; max-width: 80%; border-radius: var(--border-radius-md); border-bottom-right-radius: 4px; padding: 12px 18px; background: var(--accent); color: var(--text-white);">
                        <strong style="font-size: 0.8rem; display: block; margin-bottom: 4px; opacity: 0.8;">Anda</strong>
                        <p style="margin: 0; font-size: 0.9rem; line-height: 1.5;">${escapeHtml(text)}</p>
                        <small style="display: block; margin-top: 6px; font-size: 0.65rem; opacity: 0.7; text-align: right;">Sekarang</small>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', userBubbleHtml);
            }

            function appendTypingIndicator() {
                const messagesContainer = document.getElementById('chatbotMessages');
                const typingId = 'typing-' + Date.now();
                const typingHtml = `
                    <div id="${typingId}" class="chat-bubble chat-bubble-left" style="align-self: flex-start; max-width: 80%; display: flex; gap: 10px; border-radius: var(--border-radius-md); border-bottom-left-radius: 4px; border: 1px solid var(--border-color); padding: 16px;">
                        <div style="background: var(--accent-light); color: var(--accent); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <strong style="color: var(--primary); font-size: 0.85rem; display: block; margin-bottom: 4px;">Asisten Medicare</strong>
                            <p style="margin: 0; font-size: 0.9rem; line-height: 1.5; color: var(--text-muted); font-style: italic;">Sedang menganalisis...</p>
                        </div>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', typingHtml);
                return typingId;
            }

            function removeTypingIndicator(id) {
                const indicator = document.getElementById(id);
                if (indicator) indicator.remove();
            }

            function generateBotResponseStandard(userText) {
                const textLower = userText.toLowerCase().trim();
                
                // 1. GREETINGS INTENT
                const greetingKeywords = [
                    'halo', 'hai', 'hello', 'hi', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam', 
                    'assalamualaikum', 'pagi', 'siang', 'sore', 'malam', 'permisi', 'hey', 'p'
                ];
                const isGreeting = greetingKeywords.some(kw => {
                    if (kw.length <= 2) {
                        return textLower === kw || textLower.startsWith(kw + ' ') || textLower.endsWith(' ' + kw) || textLower.includes(' ' + kw + ' ');
                    }
                    return textLower.includes(kw);
                });

                // 2. THANKS INTENT
                const thanksKeywords = [
                    'terima kasih', 'terimakasih', 'makasih', 'suwun', 'nuhun', 'thank you', 'thanks', 'oke', 'ok', 'baik', 'sip', 'siap', 'thank'
                ];
                const isThanks = thanksKeywords.some(kw => {
                    if (kw === 'ok' || kw === 'oke') {
                        return textLower === kw || textLower.startsWith(kw + ' ') || textLower.endsWith(' ' + kw) || textLower.includes(' ' + kw + ' ');
                    }
                    return textLower.includes(kw);
                });

                // 3. APPOINTMENT / ANTREAN GUIDE INTENT
                const appointmentKeywords = [
                    'janji', 'antrean', 'antri', 'daftar', 'periksa', 'berobat', 'konsultasi langsung', 
                    'poliklinik', 'cara bertemu', 'jadwal', 'tatap muka', 'ke klinik', 'temu dokter'
                ];
                const isAppointment = appointmentKeywords.some(kw => textLower.includes(kw));

                // 4. MEDICINE / PRESCRIPTION GUIDE INTENT
                const medicineKeywords = [
                    'obat', 'resep', 'tebus', 'beli obat', 'pesan obat', 'bayar obat', 'bukti transfer', 'kirim obat', 'harga obat'
                ];
                const isMedicine = medicineKeywords.some(kw => textLower.includes(kw));

                // 5. HELP / CAPABILITIES INTENT
                const helpKeywords = [
                    'kamu bisa apa', 'fitur', 'bantuan', 'help', 'tolong', 'panduan', 'siapa kamu', 'bot', 'cara pakai', 'menu'
                ];
                const isHelp = helpKeywords.some(kw => textLower.includes(kw));

                const messagesContainer = document.getElementById('chatbotMessages');
                let botMessageContent = "";
                let matchedDoctorsHtml = "";
                let showDoctorsList = false;
                let doctorFilterType = null; // 'specialist', 'online', 'general'
                let currentSpecialization = null;

                if (isGreeting) {
                    botMessageContent = `Halo! Saya adalah <strong>Asisten Virtual Medicare</strong>. Ada yang bisa saya bantu hari ini?<br><br>
                    Anda dapat menceritakan gejala kesehatan Anda atau menanyakan panduan layanan:<br>
                    • 🩺 <strong>Gejala Penyakit</strong>: Menceritakan gejala penyakit (contoh: <em>"nyeri dada"</em>, <em>"anak demam"</em>, <em>"gigi ngilu"</em>, <em>"stres"</em>) untuk mendapatkan rekomendasi dokter spesialis.<br>
                    • 📅 <strong>Antrean & Janji Temu</strong>: Ketik <em>"buat janji"</em> atau <em>"antrean"</em> untuk panduan periksa fisik ke klinik.<br>
                    • 💊 <strong>Pesan & Tebus Obat</strong>: Ketik <em>"pesan obat"</em> atau <em>"tebus resep"</em> untuk panduan memesan obat.`;
                    
                    showDoctorsList = true;
                    doctorFilterType = 'online';
                } else if (isThanks) {
                    botMessageContent = `Sama-sama! Senang bisa membantu Anda. Jika ada keluhan kesehatan lain atau ada hal yang ingin ditanyakan lagi tentang layanan Medicare, silakan ketik di sini. Semoga sehat selalu! 😊`;
                } else if (isAppointment) {
                    botMessageContent = `Untuk melakukan pemeriksaan langsung atau konsultasi tatap muka di Medicare, silakan ikuti langkah-langkah berikut:<br><br>
                    1. 📱 Mulailah dengan melakukan konsultasi secara online dengan dokter pilihan Anda melalui tab <strong>"Konsultasi & Chat"</strong>.<br>
                    2. 🩺 Jika setelah sesi chat dokter menilai kondisi Anda memerlukan penanganan langsung di klinik, dokter akan memperbarui status konsultasi menjadi <strong>"Perlu Penanganan"</strong>.<br>
                    3. 🎫 Nomor antrean fisik Anda akan otomatis terbit dan dapat dilihat atau dicetak pada menu <strong>"Antrean Fisik"</strong>.<br>
                    4. 🏥 Datanglah ke klinik sesuai jadwal yang disepakati dengan membawa nomor antrean tersebut.`;
                    
                    showDoctorsList = true;
                    doctorFilterType = 'online';
                } else if (isMedicine) {
                    botMessageContent = `Untuk melakukan pemesanan dan penebusan obat di Medicare, berikut adalah langkah-langkahnya:<br><br>
                    1. 📝 Setelah berkonsultasi, dokter akan menulis resep obat yang tercatat di <strong>Rekam Medis</strong> Anda.<br>
                    2. 📂 Silakan buka menu <strong>"Rekam Medis"</strong> pada dashboard Anda.<br>
                    3. 💊 Klik tombol <strong>"Pesan Obat"</strong> pada riwayat rekam medis yang ingin ditebus.<br>
                    4. 💳 Setelah itu, buka menu <strong>"Pesanan Obat"</strong> untuk melihat total tagihan. Unggah bukti transfer pembayaran agar apoteker kami dapat memproses dan mengirimkan obat ke alamat Anda.`;
                } else if (isHelp) {
                    botMessageContent = `Saya adalah <strong>Asisten Virtual Medicare</strong> yang siap membantu Anda dalam menggunakan layanan kami. Berikut adalah hal-hal yang dapat saya bantu:<br><br>
                    • 🩺 <strong>Rekomendasi Dokter Spesialis</strong>: Sebutkan gejala atau keluhan Anda (misal: <em>"gatal-gatal"</em>, <em>"nyeri lambung"</em>, <em>"stres/kecemasan"</em>), dan saya akan mencarikan dokter spesialis yang sesuai.<br>
                    • 📅 <strong>Info Pendaftaran Antrean Klinik</strong>: Ketik <em>"janji temu"</em> atau <em>"daftar antrean"</em>.<br>
                    • 💊 <strong>Info Tebus Obat</strong>: Ketik <em>"tebus obat"</em> atau <em>"resep"</em>.<br><br>
                    Silakan ketik pertanyaan atau keluhan Anda!`;
                    
                    showDoctorsList = true;
                    doctorFilterType = 'online';
                } else {
                    // Try to match keywords (symptoms)
                    let bestSpecialization = null;
                    let maxMatches = 0;

                    activeKeywordMapping.forEach(mapping => {
                        let matches = 0;
                        mapping.keywords.forEach(kw => {
                            if (textLower.includes(kw)) {
                                matches++;
                            }
                        });
                        if (matches > maxMatches) {
                            maxMatches = matches;
                            bestSpecialization = mapping.specialization;
                        }
                    });

                    if (bestSpecialization && maxMatches > 0) {
                        currentSpecialization = bestSpecialization;
                        botMessageContent = `Berdasarkan keluhan Anda tentang <strong>"${escapeHtml(userText)}"</strong>, kami menyarankan Anda untuk berkonsultasi dengan Dokter Spesialis <strong>${bestSpecialization}</strong>.`;
                        showDoctorsList = true;
                        doctorFilterType = 'specialist';
                    } else {
                        // Fallback to general/internal medicine
                        botMessageContent = `Saya tidak dapat mengidentifikasi gejala penyakit spesifik dari pesan Anda.<br><br>
                        • Jika Anda sedang merasakan gejala penyakit, cobalah menggunakan kata kunci seperti <em>"nyeri dada"</em>, <em>"gigi linu"</em>, <em>"mata buram"</em>, atau <em>"stres/insomnia"</em>.<br>
                        • Jika ingin berkonsultasi secara umum terlebih dahulu, kami menyarankan Anda berkonsultasi dengan <strong>Dokter Penyakit Dalam atau Dokter Umum</strong>.`;
                        
                        showDoctorsList = true;
                        doctorFilterType = 'general';
                    }
                }

                if (showDoctorsList) {
                    if (doctorFilterType === 'specialist') {
                        const matchedDoctors = doctorsData.filter(doc => doc.spesialis && doc.spesialis.toLowerCase() === currentSpecialization.toLowerCase());
                        if (matchedDoctors.length > 0) {
                            matchedDoctorsHtml = `
                                <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                    <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 4px;">Rekomendasi Dokter Spesialis ${currentSpecialization}:</p>
                                    ${matchedDoctors.map(doc => renderDoctorCardHtml(doc)).join('')}
                                </div>
                            `;
                        } else {
                            matchedDoctorsHtml = `<p style="margin-top: 10px; font-size: 0.85rem; color: #ef4444; font-style: italic;"><i class="fas fa-exclamation-circle"></i> Saat ini belum ada dokter dengan spesialisasi tersebut yang terdaftar di MedicareSystem.</p>`;
                        }
                    } else if (doctorFilterType === 'online') {
                        // Show online/busy doctors
                        const onlineDoctors = doctorsData.filter(doc => doc.status_dokter === 'online' || doc.status_dokter === 'sibuk');
                        if (onlineDoctors.length > 0) {
                            matchedDoctorsHtml = `
                                <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                    <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 4px;">Dokter Aktif yang Bisa Dihubungi Sekarang:</p>
                                    ${onlineDoctors.slice(0, 3).map(doc => renderDoctorCardHtml(doc)).join('')}
                                </div>
                            `;
                        }
                    } else if (doctorFilterType === 'general') {
                        const pdDoctors = doctorsData.filter(doc => doc.spesialis && (doc.spesialis.toLowerCase() === 'penyakit dalam' || doc.spesialis.toLowerCase() === 'umum'));
                        if (pdDoctors.length > 0) {
                            matchedDoctorsHtml = `
                                <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                    <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 4px;">Rekomendasi Dokter Penyakit Dalam / Umum:</p>
                                    ${pdDoctors.map(doc => renderDoctorCardHtml(doc)).join('')}
                                </div>
                            `;
                        } else {
                            matchedDoctorsHtml = `
                                <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                    <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 4px;">Daftar Dokter Tersedia:</p>
                                    ${doctorsData.slice(0, 3).map(doc => renderDoctorCardHtml(doc)).join('')}
                                </div>
                            `;
                        }
                    }
                }

                const botResponseHtml = `
                    <div class="chat-bubble chat-bubble-left" style="align-self: flex-start; max-width: 80%; display: flex; gap: 10px; border-radius: var(--border-radius-md); border-bottom-left-radius: 4px; border: 1px solid var(--border-color); padding: 16px;">
                        <div style="background: var(--accent-light); color: var(--accent); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div style="flex: 1;">
                            <strong style="color: var(--primary); font-size: 0.85rem; display: block; margin-bottom: 4px;">Asisten Medicare</strong>
                            <p style="margin: 0; font-size: 0.9rem; line-height: 1.5; color: var(--text-main);">${botMessageContent}</p>
                            ${matchedDoctorsHtml}
                            <small style="display: block; margin-top: 8px; font-size: 0.65rem; color: var(--text-muted);">Sekarang</small>
                        </div>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', botResponseHtml);
            }

            function renderDoctorCardHtml(doc) {
                let statusBadge = "";
                let chatButton = "";
                
                // Parse rating
                let ratingVal = parseFloat(doc.average_rating || 0);
                let reviewCount = parseInt(doc.review_count || 0);

                if (doc.status_dokter === 'online') {
                    statusBadge = `
                        <span class="status-badge status-selesai" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; font-size: 0.65rem; background:#dcfce7; color:#15803d; border-radius:9999px;">
                            <span style="display: inline-block; width: 5px; height: 5px; background: #22c55e; border-radius: 50%;"></span>
                            Online
                        </span>
                    `;
                    chatButton = `<a href="/pasien/chat/${doc.id}" class="btn btn-sm" style="font-size: 0.7rem; padding: 4px 10px; border-radius: 9999px;"><i class="fas fa-comment-dots"></i> Chat</a>`;
                } else if (doc.status_dokter === 'sibuk') {
                    statusBadge = `
                        <span class="status-badge status-menunggu" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; font-size: 0.65rem; background:#fef3c7; color:#d97706; border-radius:9999px;">
                            <span style="display: inline-block; width: 5px; height: 5px; background: #eab308; border-radius: 50%;"></span>
                            Sibuk
                        </span>
                    `;
                    chatButton = `<a href="/pasien/chat/${doc.id}" class="btn btn-sm btn-warning" style="font-size: 0.7rem; padding: 4px 10px; border-radius: 9999px;"><i class="fas fa-comment-dots"></i> Chat (Sibuk)</a>`;
                } else {
                    statusBadge = `
                        <span class="status-badge status-batal" style="text-transform: none; display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px; font-size: 0.65rem; background:#fee2e2; color:#b91c1c; border-radius:9999px;">
                            <span style="display: inline-block; width: 5px; height: 5px; background: #ef4444; border-radius: 50%;"></span>
                            Offline
                        </span>
                    `;
                    chatButton = `<span class="btn btn-sm" style="background:#e2e8f0; color:#94a3b8; cursor:not-allowed; box-shadow:none; font-size: 0.7rem; padding: 4px 10px; border-radius: 9999px;"><i class="fas fa-ban"></i> Offline</span>`;
                }

                return `
                    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--border-radius-md); padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; gap: 10px; box-shadow: var(--shadow-sm); margin-top:8px;">
                        <div style="text-align: left;">
                            <h5 style="margin: 0; font-size: 0.85rem; color: var(--primary); font-weight: 700;">dr. ${doc.name}</h5>
                            <p style="margin: 2px 0; font-size: 0.75rem; color: var(--accent); font-weight: 600;">Spesialis ${doc.spesialis || 'Umum'}</p>
                            <div style="display: flex; align-items: center; gap: 6px; margin: 4px 0;">
                                <span style="color: #f59e0b; font-size: 0.75rem;"><i class="fas fa-star"></i> <strong>${ratingVal.toFixed(1)}</strong></span>
                                <span style="font-size: 0.65rem; color: var(--text-muted);">(${reviewCount} ulasan)</span>
                            </div>
                            <div style="margin-top: 4px;">
                                ${statusBadge}
                            </div>
                        </div>
                        <div>
                            ${chatButton}
                        </div>
                    </div>
                `;
            }

            // Scroll on load
            document.addEventListener('DOMContentLoaded', scrollToBottom);

            function escapeHtml(text) {
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            function scrollToBottom() {
                const chatMessages = document.getElementById('chatbotMessages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }
        </script>
    @endif

@endsection
