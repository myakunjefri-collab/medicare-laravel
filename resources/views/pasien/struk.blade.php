<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian Obat - #ORD-{{ $pesanan->id }}</title>
    <!-- Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Outfit', sans-serif;
            color: #1e293b;
            background: #ffffff;
            padding: 40px;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .receipt-container {
            max-width: 650px;
            margin: 0 auto;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            padding: 40px;
            position: relative;
        }
        /* Header */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px dashed #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0b2b4d;
        }
        .logo-section i {
            color: #10b981;
            font-size: 1.6rem;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0b2b4d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-title p {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 3px;
        }
        
        /* Grid Details */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .details-col h4 {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .details-col p {
            font-weight: 600;
            color: #0b2b4d;
        }
        .details-col span {
            display: block;
            color: #475569;
            font-size: 0.85rem;
            margin-top: 2px;
        }

        /* Table Resep */
        .resep-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .resep-box h3 {
            font-size: 0.9rem;
            text-transform: uppercase;
            color: #0b2b4d;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .resep-box h3 i {
            color: #10b981;
        }
        .resep-content {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0b2b4d;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        /* Price Calculation */
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.9rem;
            color: #475569;
        }
        .price-row.total {
            border-top: 2px solid #e2e8f0;
            margin-top: 10px;
            padding-top: 14px;
            font-size: 1.15rem;
            font-weight: 800;
            color: #0b2b4d;
        }

        /* Stamp Lunas */
        .stamp-lunas {
            position: absolute;
            right: 40px;
            bottom: 140px;
            border: 3px solid #10b981;
            color: #10b981;
            font-size: 1.3rem;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 8px;
            text-transform: uppercase;
            transform: rotate(-12deg);
            opacity: 0.85;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Footer */
        .receipt-footer {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .actions {
            max-width: 650px;
            margin: 20px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn {
            background: #0b2b4d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .btn:hover {
            opacity: 0.9;
        }
        .btn-success {
            background: #10b981;
        }

        @media print {
            body {
                padding: 0;
            }
            .receipt-container {
                border: none;
                padding: 0;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <!-- Stamp Lunas -->
        <div class="stamp-lunas">
            <i class="fas fa-check-circle"></i> Lunas
        </div>

        <!-- Header -->
        <div class="receipt-header">
            <div class="logo-section">
                <i class="fas fa-notes-medical"></i>
                <span>MedicareSystem</span>
            </div>
            <div class="invoice-title">
                <h2>Struk Pembelian</h2>
                <p>No. Transaksi: #ORD-{{ $pesanan->id }}</p>
            </div>
        </div>

        <!-- Details -->
        <div class="details-grid">
            <div class="details-col">
                <h4>Ditujukan Kepada:</h4>
                <p>{{ $pesanan->pasien_name }}</p>
                <span>Usia: {{ $user->age ?? '-' }} Tahun</span>
                <span>Alamat: {{ $pesanan->alamat_kirim }}</span>
            </div>
            <div class="details-col" style="text-align: right;">
                <h4>Info Pesanan:</h4>
                <p>{{ date('d/m/Y H:i', strtotime($pesanan->created_at)) }}</p>
                <span>Status Pembayaran: <strong>Lunas</strong></span>
                <span>Metode: Bank Transfer (Terverifikasi)</span>
            </div>
        </div>

        <!-- Prescription & Cost Summary -->
        <div class="resep-box">
            <h3><i class="fas fa-pills"></i> Rincian Resep & Obat</h3>
            <div class="resep-content">
                {{ $pesanan->resep }}
            </div>
            
            <div class="price-row">
                <span>Harga Obat-obatan</span>
                <span>Rp {{ number_format($pesanan->total_harga - 15000, 0, ',', '.') }}</span>
            </div>
            <div class="price-row">
                <span>Ongkos Kirim Jasa Kurir</span>
                <span>Rp 15.000</span>
            </div>
            <div class="price-row total">
                <span>Total Pembayaran</span>
                <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <p>Terima kasih telah mempercayai MedicareSystem untuk pelayanan kesehatan Anda.</p>
            <p style="margin-top: 5px;">Dokumen ini diterbitkan secara elektronik dan sah sebagai bukti pembayaran resmi.</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="actions">
        <button class="btn btn-success" onclick="window.print()"><i class="fas fa-print"></i> Cetak Dokumen</button>
        <button class="btn" onclick="window.close()"><i class="fas fa-times-circle"></i> Tutup Halaman</button>
    </div>

    <!-- Auto-print script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
