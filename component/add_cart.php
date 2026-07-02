<?php
    if(isset($_POST['add_to_cart'])){
        if($user_id != ''){
            $id = unique_id();
            $product_id = $_POST['product_id'];

            $qty = $_POST['qty'];
            $qty =filter_var($qty, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $qty = (int)$qty;

            $verify_cart = $conn->prepare("SELECT *FROM cart WHERE  user_id =? AND product_id = ?");
            $verify_cart->execute([$user_id , $product_id]);

            $max_cart_items = $conn->prepare("SELECT *FROM cart WHERE user_id =? ");
            $max_cart_items->execute([$user_id ]) ;

            $select_price = $conn->prepare("SELECT *FROM products WHERE id = ? LIMIT 1");
            $select_price->execute([$product_id]);
            $fetch_price =$select_price->fetch(PDO::FETCH_ASSOC);

            if($verify_cart->rowCount() > 0){
                $warning_msg[] = 'product already exist in your cart';
        }else if($max_cart_items->rowCount() > 20){
                $warning_msg[] = 'your cart is full';
        }else if(!$fetch_price || $fetch_price['stock'] == 0){
                $warning_msg[] = 'product is out of stock';
        }else if($qty < 1){
                $warning_msg[] = 'quantity must be at least 1';
        }else if($qty > $fetch_price['stock']){
                $warning_msg[] = 'only ' . $fetch_price['stock'] . ' left in stock';
        }else{
                $insert_cart =$conn->prepare("INSERT INTO cart (id, user_id, product_id, price, qty)
                VALUES(?, ?, ?, ?, ?)");

                $insert_cart->execute([$id, $user_id, $product_id, $fetch_price['price'], $qty]);
                $success_msg[] = 'product added to your cart successfully';
        }
    }else{
        $warning_msg[]='please login first';
    }
}
?>