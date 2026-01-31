<?php
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

$config = require __DIR__ . '/../config.php';

if (!$auth->check() || !$auth->isAdmin()) {
    redirect('../login.php');
}

$tab = (string)($_GET['tab'] ?? 'pages');
$editProductId = (int)($_GET['edit'] ?? 0);
$editNewsId = (int)($_GET['edit_news'] ?? 0);
$error = $_SESSION['_admin_error'] ?? null;
$ok = $_SESSION['_admin_ok'] ?? null;
unset($_SESSION['_admin_error'], $_SESSION['_admin_ok']);

function save_upload(array $file, string $uploadsDir): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    $name = (string)($file['name'] ?? '');
    $tmp = (string)($file['tmp_name'] ?? '');
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp','pdf'], true)) {
        return null;
    }
    if (!is_dir($uploadsDir)) {
        @mkdir($uploadsDir, 0777, true);
    }
    $base = 'u_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = rtrim($uploadsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $base;
    if (!move_uploaded_file($tmp, $dest)) {
        return null;
    }
    return 'uploads/' . $base;
}

// Handle POST actions (compact)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['_csrf'] ?? null)) {
        $_SESSION['_admin_error'] = 'CSRF error. Rifresko faqen dhe provo përsëri.';
        $failTab = match($_POST['action'] ?? '') {
            'product_save' => 'products',
            'news_save' => 'news',
            'page_save', 'page_create', 'page_delete' => 'pages',
            'contact_delete' => 'contacts',
            default => 'products',
        };
        redirect('index.php?tab=' . $failTab);
    } else {
        $action = (string)($_POST['action'] ?? '');

        try {
            if ($action === 'page_save') {
                $slug = (string)($_POST['slug'] ?? '');
                $titleP = trim((string)($_POST['title'] ?? ''));
                $body = trim((string)($_POST['body'] ?? ''));
                $db->exec(
                    'UPDATE pages SET title = ?, body = ?, updated_by = ? WHERE slug = ?',
                    [$titleP, $body, ($auth->user()['id'] ?? null), $slug]
                );
                $ok = 'Page u ruajt.';
                $tab = 'pages';
            }

            if ($action === 'product_save') {
                $id = (int)($_POST['id'] ?? 0);
                $titleP = trim((string)($_POST['title'] ?? ''));
                $desc = trim((string)($_POST['description'] ?? ''));
                $price = isset($_POST['price']) && $_POST['price'] !== '' ? (float)$_POST['price'] : null;
                $cat = isset($_POST['category']) && $_POST['category'] !== '' ? (int)$_POST['category'] : null;
                $uploadsDir = $config['uploads_dir'] ?? (__DIR__ . '/../uploads');
                $img = save_upload($_FILES['image'] ?? [], $uploadsDir);

                if ($titleP === '') {
                    $_SESSION['_admin_error'] = 'Title është i detyrueshëm.';
                    redirect('index.php?tab=products');
                }
                if ($id > 0) {
                    $sql = 'UPDATE products SET category_id=?, title=?, description=?, price=?' . ($img ? ', image_path=?' : '') . ' WHERE id=?';
                    $params = [$cat, $titleP, $desc, $price];
                    if ($img) { $params[] = $img; }
                    $params[] = $id;
                    $db->exec($sql, $params);
                    $_SESSION['_admin_ok'] = 'Product u ruajt.';
                    redirect('index.php?tab=products&edit=' . $id);
                } else {
                    $db->exec(
                        'INSERT INTO products (category_id, title, description, price, image_path, created_by) VALUES (?, ?, ?, ?, ?, ?)',
                        [$cat, $titleP, $desc, $price, $img ?? null, ($auth->user()['id'] ?? null)]
                    );
                    $_SESSION['_admin_ok'] = 'Product u shtua.';
                    redirect('index.php?tab=products');
                }
            }

            if ($action === 'product_delete') {
                $db->exec('DELETE FROM products WHERE id = ?', [(int)($_POST['id'] ?? 0)]);
                $ok = 'Product u fshi.';
                $tab = 'products';
            }

            if ($action === 'news_save') {
                $id = (int)($_POST['id'] ?? 0);
                $titleN = trim((string)($_POST['title'] ?? ''));
                $body = trim((string)($_POST['body'] ?? ''));
                $media = save_upload($_FILES['media'] ?? [], $config['uploads_dir'] ?? (__DIR__ . '/../uploads'));

                if ($id > 0) {
                    $sql = 'UPDATE news SET title=?, body=?' . ($media ? ', media_path=?' : '') . ' WHERE id=?';
                    $params = [$titleN, $body];
                    if ($media) { $params[] = $media; }
                    $params[] = $id;
                    $db->exec($sql, $params);
                } else {
                    $db->exec(
                        'INSERT INTO news (title, body, media_path, created_by) VALUES (?, ?, ?, ?)',
                        [$titleN, $body, $media, ($auth->user()['id'] ?? null)]
                    );
                }
                $ok = 'News u ruajt.';
                $tab = 'news';
            }

            if ($action === 'news_delete') {
                $db->exec('DELETE FROM news WHERE id = ?', [(int)($_POST['id'] ?? 0)]);
                $ok = 'News u fshi.';
                $tab = 'news';
            }

            if ($action === 'contact_delete') {
                $db->exec('DELETE FROM contact_messages WHERE id = ?', [(int)($_POST['id'] ?? 0)]);
                $ok = 'Mesazhi u fshi.';
                $tab = 'contacts';
            }

            if ($action === 'page_create') {
                $slug = trim((string)($_POST['slug'] ?? ''));
                $titleP = trim((string)($_POST['title'] ?? ''));
                $body = trim((string)($_POST['body'] ?? ''));
                if ($slug !== '') {
                    $db->exec(
                        'INSERT INTO pages (slug, title, body, updated_by) VALUES (?, ?, ?, ?)',
                        [$slug, $titleP, $body, ($auth->user()['id'] ?? null)]
                    );
                    $ok = 'Page u shtua.';
                }
                $tab = 'pages';
            }

            if ($action === 'page_delete') {
                $slug = (string)($_POST['slug'] ?? '');
                $protected = ['home', 'about', 'contact'];
                if (in_array($slug, $protected, true)) {
                    $error = 'Nuk mund ta fshish faqen "' . $slug . '" (e nevojshme për faqen).';
                } else {
                    $db->exec('DELETE FROM pages WHERE slug = ?', [$slug]);
                    $ok = 'Page u fshi.';
                }
                $tab = 'pages';
            }
        } catch (\Throwable $t) {
            $_SESSION['_admin_error'] = 'Gabim: ' . $t->getMessage();
            $errTab = in_array($action, ['product_save','product_delete']) ? 'products' : (in_array($action, ['news_save','news_delete']) ? 'news' : (in_array($action, ['page_save','page_create','page_delete']) ? 'pages' : 'contacts'));
            redirect('index.php?tab=' . $errTab);
        }
    }
}

$pages = $db->fetchAll('SELECT slug, title, body, updated_at FROM pages ORDER BY slug ASC');
$cats = $db->fetchAll('SELECT id, name FROM categories ORDER BY name ASC');
$products = $db->fetchAll('SELECT p.*, c.name AS category FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC');
$news = $db->fetchAll('SELECT n.*, u.name AS author FROM news n LEFT JOIN users u ON u.id=n.created_by ORDER BY n.created_at DESC');
$contacts = $db->fetchAll('SELECT * FROM contact_messages ORDER BY created_at DESC');

$editProduct = $editProductId > 0 ? $db->fetchOne('SELECT * FROM products WHERE id = ?', [$editProductId]) : null;
$editNews = $editNewsId > 0 ? $db->fetchOne('SELECT * FROM news WHERE id = ?', [$editNewsId]) : null;

$title = 'Admin Dashboard';
require __DIR__ . '/../partials/header.php';
?>

<h2>Admin Dashboard</h2>
<?php if ($ok): ?><div class="alert alert-success" role="alert"><?= e($ok) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><strong>Gabim:</strong> <?= e($error) ?></div><?php endif; ?>

<div class="mb-3">
  <a class="btn btn-sm <?= $tab==='pages'?'btn-dark':'btn-outline-dark' ?>" href="?tab=pages">Pages</a>
  <a class="btn btn-sm <?= $tab==='products'?'btn-dark':'btn-outline-dark' ?>" href="?tab=products">Products</a>
  <a class="btn btn-sm <?= $tab==='news'?'btn-dark':'btn-outline-dark' ?>" href="?tab=news">News</a>
  <a class="btn btn-sm <?= $tab==='contacts'?'btn-dark':'btn-outline-dark' ?>" href="?tab=contacts">Contacts</a>
</div>

<?php if ($tab === 'pages'): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5>Create Page (Create)</h5>
      <form method="post" class="row g-2">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="page_create">
        <div class="col-12 col-md-3"><input class="form-control" name="slug" placeholder="Slug (p.sh. faqja_ime)" required></div>
        <div class="col-12 col-md-4"><input class="form-control" name="title" placeholder="Title" required></div>
        <div class="col-12"><textarea class="form-control" name="body" rows="2" placeholder="Body" required></textarea></div>
        <div class="col-12"><button class="btn btn-sm btn-dark" type="submit">Create</button></div>
      </form>
    </div>
  </div>
  <?php foreach ($pages as $p): ?>
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><?= e($p['slug']) ?></h5>
          <div class="d-flex gap-2 align-items-center">
            <span class="small text-muted"><?= e((string)$p['updated_at']) ?></span>
            <?php if (!in_array($p['slug'], ['home','about','contact'], true)): ?>
            <form method="post" onsubmit="return confirm('Me e fshi këtë faqe?');" class="d-inline">
              <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="page_delete">
              <input type="hidden" name="slug" value="<?= e($p['slug']) ?>">
              <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
            </form>
            <?php else: ?><span class="badge bg-secondary">Protektuar</span><?php endif; ?>
          </div>
        </div>
        <form method="post" class="mt-3">
          <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="action" value="page_save">
          <input type="hidden" name="slug" value="<?= e($p['slug']) ?>">
          <div class="mb-2">
            <label class="form-label">Title (Update)</label>
            <input class="form-control" name="title" value="<?= e($p['title']) ?>" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Body</label>
            <textarea class="form-control" name="body" rows="3" required><?= e($p['body']) ?></textarea>
          </div>
          <button class="btn btn-sm btn-dark" type="submit">Update</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if ($tab === 'products'): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5><?= $editProduct ? 'Edit Product (Update)' : 'Add Product (Create)' ?><?php if ($editProduct): ?> <a href="?tab=products" class="btn btn-sm btn-outline-secondary">Anulo</a><?php endif; ?></h5>
      <form method="post" action="index.php?tab=products" enctype="multipart/form-data" class="row g-2">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="product_save">
        <input type="hidden" name="id" value="<?= $editProduct ? (int)$editProduct['id'] : 0 ?>">
        <div class="col-12 col-md-4"><input class="form-control" name="title" placeholder="Title" value="<?= $editProduct ? e($editProduct['title']) : '' ?>" required></div>
        <div class="col-12 col-md-3">
          <select class="form-select" name="category">
            <option value="">Pa kategori</option>
            <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= ($editProduct && (int)($editProduct['category_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-2"><input class="form-control" name="price" placeholder="Price" type="number" step="0.01" value="<?= $editProduct && $editProduct['price'] !== null ? e((string)$editProduct['price']) : '' ?>"></div>
        <div class="col-12 col-md-3"><input class="form-control" name="image" type="file" accept=".jpg,.jpeg,.png,.webp"><small class="text-muted"><?= $editProduct && !empty($editProduct['image_path']) ? 'Imazh aktual: ' . e($editProduct['image_path']) : 'Opsional' ?></small></div>
        <div class="col-12"><input class="form-control" name="description" placeholder="Description" value="<?= $editProduct ? e($editProduct['description'] ?? '') : '' ?>"></div>
        <div class="col-12"><button class="btn btn-sm btn-dark" type="submit"><?= $editProduct ? 'Update' : 'Create' ?></button></div>
      </form>
    </div>
  </div>

  <?php foreach ($products as $p): ?>
    <div class="card mb-2">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <div class="fw-bold"><?= e($p['title']) ?></div>
          <div class="small text-muted"><?= e($p['category'] ?? 'Pa kategori') ?> · <?= $p['price'] !== null ? e((string)$p['price']).' €' : '' ?></div>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-dark" href="?tab=products&edit=<?= (int)$p['id'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Me e fshi?');" class="d-inline">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="product_delete">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if ($tab === 'news'): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5><?= $editNews ? 'Edit News (Update)' : 'Add News (Create)' ?><?php if ($editNews): ?> <a href="?tab=news" class="btn btn-sm btn-outline-secondary">Anulo</a><?php endif; ?></h5>
      <form method="post" enctype="multipart/form-data" class="vstack gap-2">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="news_save">
        <input type="hidden" name="id" value="<?= $editNews ? (int)$editNews['id'] : 0 ?>">
        <input class="form-control" name="title" placeholder="Title" value="<?= $editNews ? e($editNews['title']) : '' ?>" required>
        <textarea class="form-control" name="body" rows="3" placeholder="Body" required><?= $editNews ? e($editNews['body']) : '' ?></textarea>
        <input class="form-control" name="media" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf">
        <small class="text-muted"><?= $editNews && !empty($editNews['media_path']) ? 'File aktual: ' . e($editNews['media_path']) : 'Opsional' ?></small>
        <button class="btn btn-sm btn-dark" type="submit"><?= $editNews ? 'Update' : 'Create' ?></button>
      </form>
    </div>
  </div>

  <?php foreach ($news as $n): ?>
    <div class="card mb-2">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <div class="fw-bold"><?= e($n['title']) ?></div>
          <div class="small text-muted"><?= e($n['author'] ?? 'Anonim') ?> · <?= e((string)$n['created_at']) ?></div>
        </div>
        <div class="d-flex gap-2">
          <?php if (!empty($n['media_path'])): ?>
            <a class="btn btn-sm btn-outline-dark" href="../<?= e($n['media_path']) ?>" target="_blank" rel="noreferrer">File</a>
          <?php endif; ?>
          <a class="btn btn-sm btn-outline-dark" href="?tab=news&edit_news=<?= (int)$n['id'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Me e fshi?');" class="d-inline">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="news_delete">
            <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if ($tab === 'contacts'): ?>
  <p class="text-muted small mb-3">Read mesazhet dhe Delete kur të nevojitet.</p>
  <?php foreach ($contacts as $c): ?>
    <div class="card mb-2">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="fw-bold"><?= e($c['name']) ?> <span class="small text-muted">(<?= e($c['email']) ?>)</span></div>
            <div class="small text-muted"><?= e((string)$c['created_at']) ?></div>
          </div>
          <form method="post" onsubmit="return confirm('Me e fshi këtë mesazh?');" class="d-inline">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="contact_delete">
            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
          </form>
        </div>
        <p class="mb-0 mt-2"><?= nl2br(e($c['message'])) ?></p>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($contacts)): ?><p class="text-muted">Asnjë mesazh.</p><?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>

