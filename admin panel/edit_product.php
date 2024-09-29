<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    header('location:login.php');
    exit(); // หยุดการทำงานหลังจากเปลี่ยนเส้นทาง
}

// ตรวจสอบว่ามีค่า 'id' ใน $_GET หรือไม่
$product_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_STRING) : '';

$fetch_products = null; // กำหนดค่าเริ่มต้น

if ($product_id) {
    // เตรียมและดำเนินการคำสั่งเพื่อดึงข้อมูลผลิตภัณฑ์
    $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ? AND seller_id = ?");
    $select_products->execute([$product_id, $seller_id]);

    // ตรวจสอบจำนวนแถวที่ดึงมา
    if ($select_products->rowCount() > 0) {
        $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
    }
}

if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $product_id = filter_var($product_id, FILTER_SANITIZE_STRING);

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);

    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);

    $stock = $_POST['stock'];
    $stock = filter_var($stock, FILTER_SANITIZE_STRING);
    $status = isset($_POST['status']) ? filter_var($_POST['status'], FILTER_SANITIZE_STRING) : 'active';

    // แก้ไขคำสั่ง SQL UPDATE
    $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, product_detail = ?, stock = ?, status = ? WHERE id = ?");
    $update_product->execute([$name, $price, $description, $stock, $status, $product_id]);

    $success_msg[] = 'Product updated';

    $old_image = $_POST['old_image'];
    $image = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/' . $image;

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $warning_msg[] = 'Image size is too large';
        } else {
            // ตรวจสอบรูปภาพที่มีชื่อซ้ำ
            $select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ? AND seller_id = ?");
            $select_image->execute([$image, $seller_id]);

            if ($select_image->rowCount() > 0) {
                $warning_msg[] = 'Please rename your image';
            } else {
                $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
                $update_image->execute([$image, $product_id]);
                move_uploaded_file($image_tmp_name, $image_folder);

                if ($old_image != $image && $old_image != '') {
                    unlink('../uploaded_files/' . $old_image);
                }
                $success_msg[] = 'Image updated!';
            }
        }
    }
}

// delete image
if (isset($_POST['delete_image'])) {
    $empty_image = '';

    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_STRING);

    $delete_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_image->execute([$product_id]);

    if ($fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($fetch_delete_image['image'])) {
            unlink('../uploaded_files/' . $fetch_delete_image['image']);
            $unset_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
            $unset_image->execute([$empty_image, $product_id]);
            $success_msg[] = 'Image deleted successfully';
        }
    }
}

// delete product
if (isset($_POST['delete_product'])) {
    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_STRING);

    $delete_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_image->execute([$product_id]);
    $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);

    if (!empty($fetch_delete_image['image'])) {
        unlink('../uploaded_files/' . $fetch_delete_image['image']);
    }
    
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$product_id]);
    $success_msg[] = 'Product deleted successfully!';
    header('location:view_product.php');
    exit(); // หยุดการทำงานหลังจากเปลี่ยนเส้นทาง
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Seller dashboard Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css">
    <!-- Font -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
<div class="main-container">
    <?php include '../component/admin_header.php'; ?>
    <section class="post-editor">
        <div class="heading">
            <h1>Edit Product</h1>
            <img src="../image/separator-img.png" alt="Separator">
        </div>
        <div class="box-container">
            <?php if ($fetch_products): ?>
                <div class="form-container">
                    <form action="" method="post" enctype="multipart/form-data" class="register">
                        <input type="hidden" name="old_image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($fetch_products['id']); ?>">
                        <div class="input-field">
                            <p>Product Status<span>*</span></p>
                            <select name="status" class="box">
                                <option value="<?= htmlspecialchars($fetch_products['status']); ?>" selected><?= htmlspecialchars($fetch_products['status']); ?></option>
                                <option value="active">Active</option>
                                <option value="deactive">Deactive</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Product Name<span>*</span></p>
                            <input type="text" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Product Price<span>*</span></p>
                            <input type="number" name="price" value="<?= htmlspecialchars($fetch_products['price']); ?>" class="box">
                        </div>
                        <div class="input-field">
                            <p>Product Description<span>*</span></p>
                            <textarea name="description" class="box"><?= htmlspecialchars($fetch_products['product_detail']); ?></textarea>
                        </div>
                        <div class="input-field">
                            <p>Product Stock<span>*</span></p>
                            <input type="number" name="stock" value="<?= htmlspecialchars($fetch_products['stock']); ?>" class="box" min="0" max="9999999999" maxlength="10">
                        </div>
                        <div class="input-field">
                            <p>Product Image<span>*</span></p>
                            <input type="file" name="image" class="box" accept="image/*">
                            <?php if (!empty($fetch_products['image'])): ?>
                                <img src="../uploaded_files/<?= htmlspecialchars($fetch_products['image']); ?>" class="image">
                                <div class="flex-btn">
                                    <input type="submit" name="delete_image" class="btn" value="Delete Image">
                                    <a href="view_product.php" class="btn" style="width:49%; text-align:center; height: 3rem; margin-top: .7rem;">Go Back</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-btn">
                            <input type="submit" name="update" value="Update Product" class="btn">
                            <input type="submit" name="delete_product" value="Delete Product" class="btn">
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="empty">
                    <p>No product found!</p>
                </div>
                <br><br>
                <div class="flex-btn">
                    <a href="view_product.php" class="btn">View Product</a>
                    <a href="add_products.php" class="btn">Add Product</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../js/admin_script.js"></script>

<?php include '../component/alert.php'; ?>
</body>
</html>
