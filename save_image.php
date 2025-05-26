<?php
// Create images directory if it doesn't exist
if (!file_exists('images')) {
    mkdir('images', 0777, true);
}

// Create products directory if it doesn't exist
if (!file_exists('images/products')) {
    mkdir('images/products', 0777, true);
}

// The base64 image data
$base64_image = '/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxISEBUQEhISFRUVFRUXFhUVFxYVFRUQFRYWFxUVFRUYHSggGBolGxUVITEhJikrLi4uFx8zODUtOCgtLisBCgoKDg0OGhAQGy0lICUtLS0tLS0tLS4tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS4tLS0tLS0tLS0tLS0tLf/AABEIALkBEAMBEQACEQEDEQH/xAAcAAABBQEBAQAAAAAAAAAAAAAAAQIDBAYFBwj/xABNEAACAQIDAwUKCgYJBAMAAAABAgMAEQQSIQUxUQYTIkFhBzJSVHKRk6HS0xQWFzVCcoGUs9EjU2JzkrEVM0OCg6KywcI0RGPwhOHx/8QAGgEBAAMBAQEAAAAAAAAAAAAAAAIDBAEFBv/EADoRAAIBAgMEBgkDBAIDAAAAAAABAgMRBBIhFDFBURMiYXGRoQUyU4GSsdHh8FJUwSMzQrIkQ2Ki8f/aAAwDAQACEQMRAD8A9xoAoAoDMd0rFyRbKxEkTsjgIAyEqwDSorWYag2Yi411oDxOLGYsgH4di9R+um9uvco+iI1KcZ5nqr7jlzt7Dw8koZpdqYqMLYBRLIzux3BVL+vdVVf0W4NKDbv2aL3kJTa1Jdv7K2hhspXGYt1cFgDJKJAo16ahzbr17DuquhgadW/X3d1vcShJS36Gbk21jR/3eJ9LL7VWz9FqP+Xkao0E+JEeUWMH/dYj00vtVnlgkuJasInxGnlNjPGcR6aX2qg8IuZ3Y1zGfGnGeM4j00vtVHZlzGxrmKOVmMH/AHE/ppfaps65nNkXMX434z9fP6aX2q7sy5nNlXMPjjjP183ppfars65nNmXMX5PJz/Z4v7rL7IpsuF4VvIlsmDe6t5Eb9zrFdSYr7pNXNkw/CsvBnNiw3CuvBlCbkdKpytzykdRws4PmtXNkoe2XgxsOH/cR8H9SI8k38KX7tNXdjoe2Xgzuw4f9xHwYnxTk8KX7tLTY6Htl4MbDhv3EfBnX2d3McXMAUE+u4Nh2jFuOaR1FuvfVc8PQj/2p9yZVPC4eP/cn3JnpHIvuNphZY8TipzKyEMsSpzcoc G6l2DEtawNhYX4jfjdr6GGVr6HrFcOBQBQBQBQBQBQBQBQBQBQBQBQBQBQHJ5T/9TPmH+9AeaUAUB2OTH9cv14/9VAelUAUAUAUAUAUAUAUAUAUAUAUAUAUB//Z';

// Remove the data:image/jpeg;base64, prefix if present
$base64_image = str_replace('data:image/jpeg;base64,', '', $base64_image);

// Decode base64 image
$image_data = base64_decode($base64_image);

// Save the image
$file_path = 'images/products/gaming-keyboard.jpg';
if (file_put_contents($file_path, $image_data)) {
    echo "Image saved successfully to: " . $file_path;
} else {
    echo "Error saving image";
}
?> 