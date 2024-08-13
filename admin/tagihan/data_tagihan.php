<?php
// Ambil data dari form
$bulan = isset($_POST["bulan"]) ? $_POST["bulan"] : '';
$tahun = isset($_POST["tahun"]) ? $_POST["tahun"] : '';
if(empty($bulan) || empty($tahun)) {
    die("Bulan dan Tahun harus diisi.");
}

// Query untuk mendapatkan nama bulan
$sql = $koneksi->query("SELECT bulan FROM tb_bulan WHERE id_bulan='$bulan'");
if(!$sql) {
    die("Query Error: " . $koneksi->error);
}

$data = $sql->fetch_assoc();
if (!$data) {
    die("Data untuk bulan yang dipilih tidak ditemukan.");
} else {
    $bl = $data['bulan'];
}
?>

<section class="content">
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-info"></i> Data Tagihan</h4>
        <h4>Bulan : <?php echo $bl; ?> - Tahun : <?php echo $tahun; ?></h4>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">DATA TAGIHAN</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove">
                    <i class="fa fa-remove"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID PELANGGAN</th>
                            <th>Nama</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $sql = $koneksi->query("SELECT p.id_pelanggan, p.nama, p.no_hp, t.id_tagihan, t.tagihan, t.status, t.tgl_bayar 
                            FROM tb_pelanggan p 
                            INNER JOIN tb_tagihan t ON p.id_pelanggan=t.id_pelanggan 
                            WHERE t.bulan='$bulan' AND t.tahun='$tahun' AND t.status='BL' 
                            ORDER BY t.status ASC");

                        if(!$sql) {
                            die("Query Error: " . $koneksi->error);
                        }

                        if($sql->num_rows == 0) {
                            echo "<tr><td colspan='6'>Tidak ada data tagihan untuk bulan dan tahun yang dipilih.</td></tr>";
                        } else {
                            while ($data = $sql->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $data['id_pelanggan'] . "</td>";
                                echo "<td>" . $data['nama'] . "</td>";
                                echo "<td>" . rupiah($data['tagihan']) . "</td>";
                                echo "<td>";
                                if ($data['status'] == 'BL') {
                                    echo "<span class='label label-danger'>Belum Bayar</span>";
                                } elseif ($data['status'] == 'LS') {
                                    echo "<span class='label label-primary'>Lunas</span> (" . $data['tgl_bayar'] . ")";
                                }
                                echo "</td>";
                                echo "<td>";
                                echo "<a href='?page=bayar-tagihan&kode=" . $data['id_tagihan'] . "' title='Bayar Tagihan' class='btn btn-info'><i class='glyphicon glyphicon-ok'></i> BAYAR</a>";
                                echo "<a href='https://api.whatsapp.com/send?phone=" . $data['no_hp'] . "&text=Salam,%20Bpk/Ibu/Sdr/i%20" . $data['nama'] . ",%0A
                                Mohon%20untuk%20melakukan%20pembayaran%20Tagihan%20Internet%20untuk%20Bulan%20" . $bulan . "%20Tahun%20" . $tahun . ".%20Pembayaran%20bisa%20dengan%20transfer%20ke:%0ARek. Mandiri%0ANo:%200383888888%0A a.n:%20AdminKtmNet%0A%0AKirim%20Bukti%20Pembayaran%20ke%20WA%20ini.%20Terima%20kasih%0A%0A*Admin KTM Cell*' target='_blank' title='Pesan WhatsApp' class='btn btn-gray'><img src='dist/img/wa2.png'></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <div class="box-footer">
                    <a href="?page=buka-tagihan" class="btn btn-primary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</section>
