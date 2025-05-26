<?php
// This script will fix the product.php file by adding a check for empty image values

$file_path = 'product.php';
$content = file_get_contents($file_path);

// Find the image tag line
$pattern = '/<img src="<?php echo htmlspecialchars\(\$product\[\'image_url\'\]\); ?>"\s+alt="<?php echo htmlspecialchars\(\$product\[\'name\'\]\); ?>"\s+class="img-fluid rounded">/';

// Replace with the fixed version that checks for empty values
$replacement = '<?php if (!empty($product[\'image_url\'])): ?>
                    <img src="<?php echo htmlspecialchars($product[\'image_url\']); ?>" 
                         alt="<?php echo htmlspecialchars($product[\'name\']); ?>"
                         class="img-fluid rounded">
                <?php else: ?>
                    <img src="images/no-image.jpg" 
                         alt="<?php echo htmlspecialchars($product[\'name\']); ?>"
                         class="img-fluid rounded">
                <?php endif; ?>';

$new_content = preg_replace($pattern, $replacement, $content);

// Write the fixed content back to the file
file_put_contents($file_path, $new_content);

echo "Product.php file has been fixed to handle null image values.";
?> 