<?php
require 'config.php';
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'tambah') {
            $stmt = $pdo->prepare("INSERT INTO produk (id_kategori,nama_produk,harga,stok) VALUES (?,?,?,?)");
            $stmt->execute([(int)$_POST['id_kategori'], trim($_POST['nama_produk']), $_POST['harga'], (int)$_POST['stok']]);
            header('Location: index.php?pesan=tambah'); exit;
        }
        if ($action === 'ubah') {
            $stmt = $pdo->prepare("UPDATE produk SET id_kategori=?, nama_produk=?, harga=?, stok=? WHERE id_produk=?");
            $stmt->execute([(int)$_POST['id_kategori'], trim($_POST['nama_produk']), $_POST['harga'], (int)$_POST['stok'], (int)$_POST['id_produk']]);
            header('Location: index.php?pesan=ubah'); exit;
        }
        if ($action === 'hapus') {
            $stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk=?");
            $stmt->execute([(int)$_POST['id_produk']]);
            header('Location: index.php?pesan=hapus'); exit;
        }
    } catch (PDOException $ex) {
        $error = "Aksi gagal: " . $ex->getMessage();
    }
}
$kategori = $pdo->query("SELECT id_kategori,nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll();
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}
$cari = trim($_GET['cari'] ?? '');
$sql = "SELECT p.id_produk,p.nama_produk,k.nama_kategori,p.harga,p.stok
        FROM produk p INNER JOIN kategori k ON p.id_kategori=k.id_kategori";
$params = [];
if ($cari !== '') {
    $sql .= " WHERE p.nama_produk LIKE ? OR k.nama_kategori LIKE ?";
    $params = ["%$cari%", "%$cari%"];
}
$sql .= " ORDER BY p.id_produk";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $produk = $stmt->fetchAll();
$jumlahKategori = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$jumlahProduk = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$ringkasan = $pdo->query("SELECT k.nama_kategori,COUNT(p.id_produk) AS jumlah_produk
    FROM kategori k LEFT JOIN produk p ON p.id_kategori=k.id_kategori
    GROUP BY k.id_kategori,k.nama_kategori ORDER BY k.nama_kategori")->fetchAll();
$pesan = ['tambah'=>'Produk berhasil ditambahkan.','ubah'=>'Data produk berhasil diperbarui.','hapus'=>'Produk berhasil dihapus.'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Produk | Web1 Pertemuan 09</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f6fb;color:#172033;font:15px Arial,sans-serif}
header{background:#173b67;color:white;padding:25px max(22px,calc((100% - 1120px)/2))}
header h1{margin:0 0 7px;font-size:25px}header p{margin:0;color:#dce8f7}
main{max-width:1120px;margin:25px auto;padding:0 18px}.stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px;margin-bottom:18px}
.stat,.panel{background:white;border:1px solid #e1e7f0;border-radius:12px;padding:18px;box-shadow:0 3px 12px #152c4d0a}
.stat span{color:#68778c}.stat strong{display:block;font-size:27px;margin-top:8px;color:#173b67}
.layout{display:grid;grid-template-columns: minmax(260px, .8fr) minmax(0, 1.7fr);gap:18px}
h2{font-size:18px;margin:0 0 15px}.field{margin-bottom:12px}label{display:block;font-weight:bold;margin-bottom:6px}
input,select{width:100%;padding:10px;border:1px solid #ccd5e2;border-radius:7px;font:inherit}
button,.btn{border:0;border-radius:7px;padding:10px 13px;background:#1764ad;color:white;font-weight:bold;cursor:pointer;text-decoration:none;display:inline-block}
.secondary{background:#e8eef6;color:#23415f}.danger{background:#c43c47}.actions{display:flex;gap:6px;align-items:center}
table{border-collapse:collapse;width:100%;min-width:590px}th,td{padding:12px 10px;text-align:left;border-bottom:1px solid #e8edf4}th{background:#f5f8fc;color:#53647b;font-size:13px}
.table-wrap{overflow-x:auto}.search{display:flex;gap:8px;margin-bottom:12px}.search input{flex:1}
.notice{padding:11px 13px;background:#e8f7ee;color:#17633a;border-radius:8px;margin-bottom:15px}.error{background:#fff0f0;color:#9a2525}
.summary{display:grid;grid-template-columns:repeat(auto-fit,minmax(145px,1fr));gap:10px}.summary div{background:#f5f8fc;padding:12px;border-radius:8px}.summary b{display:block;margin-top:5px;font-size:20px;color:#173b67}
small{color:#68778c}.empty{text-align:center;color:#718096;padding:25px}
@media(max-width:800px){.layout{grid-template-columns:1fr}.stats{grid-template-columns:1fr 1fr}}
</style></head>
<body>
<header><h1>Data Produk</h1><p>Praktikum Pemrograman Web I — Pertemuan 9 · Database web1_pertemuan09</p></header>
<main>
<?php if(isset($_GET['pesan']) && isset($pesan[$_GET['pesan']])): ?><div class="notice"><?=e($pesan[$_GET['pesan']])?></div><?php endif ?>
<?php if(!empty($error)): ?><div class="notice error"><?=e($error)?></div><?php endif ?>
<section class="stats">
 <div class="stat"><span>Total kategori</span><strong><?=e($jumlahKategori)?></strong></div>
 <div class="stat"><span>Total produk</span><strong><?=e($jumlahProduk)?></strong></div>
</section>
<div class="layout">
<section class="panel">
<h2><?=$edit ? 'Ubah Produk' : 'Tambah Produk'?></h2>
<form method="post">
<input type="hidden" name="action" value="<?=$edit?'ubah':'tambah'?>">
<?php if($edit): ?><input type="hidden" name="id_produk" value="<?=e($edit['id_produk'])?>"><?php endif ?>
<div class="field"><label for="nama_produk">Nama produk</label><input id="nama_produk" name="nama_produk" required maxlength="120" value="<?=e($edit['nama_produk']??'')?>"></div>
<div class="field"><label for="id_kategori">Kategori</label><select id="id_kategori" name="id_kategori" required><option value="">Pilih kategori</option>
<?php foreach($kategori as $k): ?><option value="<?=e($k['id_kategori'])?>" <?=isset($edit['id_kategori']) && $edit['id_kategori']==$k['id_kategori']?'selected':''?>><?=e($k['nama_kategori'])?></option><?php endforeach ?>
</select></div>
<div class="field"><label for="harga">Harga (Rp)</label><input id="harga" name="harga" type="number" min="0" step="0.01" required value="<?=e($edit['harga']??'')?>"></div>
<div class="field"><label for="stok">Stok</label><input id="stok" name="stok" type="number" min="0" step="1" required value="<?=e($edit['stok']??'')?>"></div>
<button type="submit"><?=$edit?'Simpan':'Tambah'?></button>
<?php if($edit): ?> <a class="btn secondary" href="index.php">Batal</a><?php endif ?>
</form>
</section>
<section class="panel">
<h2>Daftar Produk</h2>
<form class="search" method="get"><input name="cari" placeholder="Cari nama produk atau kategori..." value="<?=e($cari)?>"><button type="submit">Cari</button><?php if($cari!==''): ?><a class="btn secondary" href="index.php">Reset</a><?php endif ?></form>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead><tbody>
<?php if(!$produk): ?><tr><td colspan="6" class="empty">Data produk tidak ditemukan.</td></tr>
<?php else: foreach($produk as $p): ?><tr>
<td><?=e($p['id_produk'])?></td><td><b><?=e($p['nama_produk'])?></b></td><td><?=e($p['nama_kategori'])?></td>
<td>Rp <?=number_format((float)$p['harga'],0,',','.')?></td><td><?=e($p['stok'])?></td>
<td><div class="actions"><a class="btn secondary" href="?edit=<?=e($p['id_produk'])?>">Edit</a>
<form method="post" onsubmit="return confirm('Yakin ingin menghapus produk ini?')"><input type="hidden" name="action" value="hapus"><input type="hidden" name="id_produk" value="<?=e($p['id_produk'])?>"><button class="danger" type="submit">Hapus</button></form></div></td>
</tr><?php endforeach; endif ?>
</tbody></table></div>
</section>
</div>
<section class="panel" style="margin-top:18px"><h2>Jumlah Produk per Kategori</h2><div class="summary">
<?php foreach($ringkasan as $r): ?><div><?=e($r['nama_kategori'])?><b><?=e($r['jumlah_produk'])?> produk</b></div><?php endforeach ?>
</div></section>
<p><small>Data ditampilkan dari relasi tabel produk dan kategori menggunakan INNER JOIN. Ringkasan kategori menggunakan LEFT JOIN agar kategori kosong tetap terlihat.</small></p>
</main></body></html>
