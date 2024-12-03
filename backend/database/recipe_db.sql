-- If exsist tables, drop them
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `auditrecord`;
DELETE FROM `categories`;
DELETE FROM `comment`;
DELETE FROM `ingredient`;
DELETE FROM `liked`;
DELETE FROM `recipe`;
DELETE FROM `recipe_categories`;
DELETE FROM `users`;

SET FOREIGN_KEY_CHECKS = 1;


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 03, 2024 at 01:14 AM
-- Server version: 8.0.39
-- PHP Version: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "-08:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recipe_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `auditrecord`
--

CREATE TABLE `auditrecord` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `method` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `error_message` varchar(10000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip_address` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` longblob,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `created_at`) VALUES
(1, 'Breakfast', NULL, '2024-12-01 22:59:50'),
(2, 'Lunch', NULL, '2024-12-01 22:59:50'),
(3, 'Dinner', NULL, '2024-12-01 22:59:50'),
(4, 'Appetizer', NULL, '2024-12-01 22:59:50'),
(5, 'Pasta', NULL, '2024-12-01 22:59:50'),
(6, 'Soup', NULL, '2024-12-01 22:59:50'),
(7, 'Vegetarian', NULL, '2024-12-01 22:59:50'),
(8, 'Seafood', NULL, '2024-12-01 22:59:50'),
(9, 'Dessert', NULL, '2024-12-01 22:59:50'),
(10, 'Salad', NULL, '2024-12-01 22:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int NOT NULL,
  `comment` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `recipe_id` int NOT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `comment`, `recipe_id`, `created_by`, `created_at`) VALUES
(1, 'Great simple recipe! I added extra garlic.', 1, 7, '2024-12-01 22:59:50'),
(2, 'Perfect weeknight dinner!', 1, 4, '2024-12-01 22:59:50'),
(3, 'Classic recipe, love the dressing!', 2, 7, '2024-12-01 22:59:50'),
(4, 'Added grilled chicken to make it a meal.', 2, 4, '2024-12-01 22:59:50'),
(5, 'These turned out so fluffy!', 3, 4, '2024-12-01 22:59:50'),
(6, 'Best weekend breakfast recipe.', 3, 7, '2024-12-01 22:59:50'),
(7, 'Authentic Italian recipe!', 4, 4, '2024-12-01 22:59:50'),
(8, 'Creamy and delicious.', 4, 7, '2024-12-01 22:59:50'),
(9, 'Perfect texture and sweetness.', 5, 4, '2024-12-01 22:59:50'),
(10, 'Made these with my kids, huge hit!', 5, 7, '2024-12-01 22:59:50'),
(11, 'Simple but delicious breakfast.', 6, 7, '2024-12-01 22:59:50'),
(12, 'Added red pepper flakes for extra kick.', 6, 4, '2024-12-01 22:59:50'),
(13, 'Better than takeout!', 7, 7, '2024-12-01 22:59:50'),
(14, 'Great base recipe to customize.', 7, 4, '2024-12-01 22:59:50'),
(15, 'Perfect post-workout drink.', 8, 4, '2024-12-01 22:59:50'),
(16, 'Added protein powder - delicious!', 8, 7, '2024-12-01 22:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `ingredient`
--

CREATE TABLE `ingredient` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `recipe_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredient`
--

INSERT INTO `ingredient` (`id`, `name`, `recipe_id`, `created_at`) VALUES
(1, 'pasta 300g', 1, '2024-12-01 22:59:50'),
(2, 'bacon 3 slices', 1, '2024-12-01 22:59:50'),
(3, '1 egg', 1, '2024-12-01 22:59:50'),
(4, 'some cheese', 1, '2024-12-01 22:59:50'),
(5, 'lettuce 300g', 2, '2024-12-01 22:59:50'),
(6, 'croutons', 2, '2024-12-01 22:59:50'),
(7, 'parmesan cheese', 2, '2024-12-01 22:59:50'),
(8, 'caesar dressing', 2, '2024-12-01 22:59:50'),
(9, '2 eggs', 3, '2024-12-01 22:59:50'),
(10, 'flour 50g', 3, '2024-12-01 22:59:50'),
(11, 'sugar 20g', 3, '2024-12-01 22:59:50'),
(12, 'baking powder 1 tb', 3, '2024-12-01 22:59:50'),
(13, 'spaghetti 300g', 4, '2024-12-01 22:59:50'),
(14, 'eggs', 4, '2024-12-01 22:59:50'),
(15, 'Parmesan cheese', 4, '2024-12-01 22:59:50'),
(16, 'pancetta', 4, '2024-12-01 22:59:50'),
(17, 'butter', 5, '2024-12-01 22:59:50'),
(18, 'sugar', 5, '2024-12-01 22:59:50'),
(19, 'brown sugar', 5, '2024-12-01 22:59:50'),
(20, 'eggs', 5, '2024-12-01 22:59:50'),
(21, 'vanilla', 5, '2024-12-01 22:59:50'),
(22, 'flour', 5, '2024-12-01 22:59:50'),
(23, 'chocolate chips', 5, '2024-12-01 22:59:50'),
(24, 'bread', 6, '2024-12-01 22:59:50'),
(25, 'ripe avocado', 6, '2024-12-01 22:59:50'),
(26, 'lemon juice', 6, '2024-12-01 22:59:50'),
(27, 'olive oil', 6, '2024-12-01 22:59:50'),
(28, 'pizza dough', 7, '2024-12-01 22:59:50'),
(29, 'tomato sauce', 7, '2024-12-01 22:59:50'),
(30, 'cheese', 7, '2024-12-01 22:59:50'),
(31, 'toppings of choice', 7, '2024-12-01 22:59:50'),
(32, 'banana', 8, '2024-12-01 22:59:50'),
(33, 'milk', 8, '2024-12-01 22:59:50'),
(34, 'yogurt', 8, '2024-12-01 22:59:50'),
(35, 'honey', 8, '2024-12-01 22:59:50'),
(36, 'ice cubes', 8, '2024-12-01 22:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `liked`
--

CREATE TABLE `liked` (
  `id` int NOT NULL,
  `recipe_id` int NOT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `liked`
--

INSERT INTO `liked` (`id`, `recipe_id`, `created_by`, `created_at`) VALUES
(1, 1, 7, '2024-12-01 22:59:50'),
(2, 1, 4, '2024-12-01 22:59:50'),
(3, 2, 7, '2024-12-01 22:59:50'),
(4, 2, 4, '2024-12-01 22:59:50'),
(5, 3, 4, '2024-12-01 22:59:50'),
(6, 3, 7, '2024-12-01 22:59:50'),
(7, 4, 4, '2024-12-01 22:59:50'),
(8, 4, 7, '2024-12-01 22:59:50'),
(9, 5, 7, '2024-12-01 22:59:50'),
(10, 5, 4, '2024-12-01 22:59:50'),
(11, 6, 7, '2024-12-01 22:59:50'),
(12, 6, 4, '2024-12-01 22:59:50'),
(13, 7, 4, '2024-12-01 22:59:50'),
(14, 7, 7, '2024-12-01 22:59:50'),
(15, 8, 7, '2024-12-01 22:59:50'),
(16, 8, 4, '2024-12-01 22:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `id` int NOT NULL,
  `name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`id`, `name`, `description`, `is_active`, `created_by`, `created_at`) VALUES
(1, 'Tomato Spaghetti', 'Boil pasta, cook bacon, mix eggs and cheese...', 1, 4, '2024-01-01 08:00:00'),
(2, 'Caesar Salad', 'Chop lettuce, add dressing, mix croutons...', 1, 4, '2024-01-02 08:00:00'),
(3, 'Pancakes', '1. In a bowl, mix flour, sugar, baking powder, and salt.\n2. In another bowl, whisk together milk, eggs, and melted butter.\n3. Combine the wet and dry ingredients until smooth.\n4. Heat a non-stick pan and pour batter to form pancakes.\n5. Cook until bubbles form, then flip and cook the other side.\n6. Serve warm with syrup or your favorite toppings.', 1, 7, '2024-02-04 08:00:00'),
(4, 'Spaghetti Carbonara', '1. Cook spaghetti in salted boiling water until al dente.\n2. In a bowl, whisk eggs, Parmesan cheese, salt, and pepper.\n3. Cook diced pancetta or bacon in a skillet until crispy.\n4. Drain the pasta, reserving some pasta water.\n5. Toss hot pasta with the egg mixture and pancetta, adding pasta water as needed.\n6. Serve immediately, topped with extra cheese.', 1, 7, '2024-02-15 08:00:00'),
(5, 'Chocolate Chip Cookies', '1. Preheat the oven to 350°F (175°C).\n2. Cream butter, sugar, and brown sugar until light and fluffy.\n3. Mix in eggs and vanilla extract.\n4. Gradually add flour, baking soda, and salt. Stir in chocolate chips.\n5. Scoop dough onto a baking sheet and bake for 10-12 minutes.\n6. Let cool slightly before serving.', 1, 7, '2024-02-06 08:00:00'),
(6, 'Avocado Toast', '1. Toast a slice of bread to your desired crispness.\n2. Mash a ripe avocado with salt, pepper, and a squeeze of lemon juice.\n3. Spread the mashed avocado onto the toast.\n4. Add toppings like a poached egg, sliced tomato, or chili flakes.\n5. Drizzle with olive oil for extra flavor.\n6. Enjoy immediately!', 1, 4, '2024-04-07 07:00:00'),
(7, 'Homemade Pizza', '1. Preheat your oven to the highest temperature.\n2. Roll out pizza dough and place it on a baking tray.\n3. Spread tomato sauce evenly over the dough.\n4. Add your favorite toppings and shredded cheese.\n5. Bake in the oven for 10-12 minutes or until the crust is golden and crispy.\n6. Slice and serve hot.', 1, 4, '2024-08-08 07:00:00'),
(8, 'Banana Smoothie', '1. Peel and slice a ripe banana.\n2. Add the banana, milk, yogurt, and honey to a blender.\n3. Add a handful of ice cubes for a cold, refreshing drink.\n4. Blend until smooth and creamy.\n5. Pour into a glass and garnish with a slice of banana.\n6. Serve immediately and enjoy!', 1, 7, '2024-09-09 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_categories`
--

CREATE TABLE `recipe_categories` (
  `recipe_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_categories`
--

INSERT INTO `recipe_categories` (`recipe_id`, `category_id`) VALUES
(6, 1),
(8, 1),
(4, 2),
(7, 2),
(1, 3),
(4, 3),
(7, 3),
(6, 7),
(3, 9),
(5, 9),
(8, 9),
(2, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile` longblob,
  `role` enum('admin','editor','viewer') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `profile`, `role`, `created_at`) VALUES
(1, 'kaiki_admin', 'Kaiki', 'Kano', 'kaiki_admin@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, 'admin', '2024-12-01 22:59:50'),
(2, 'kaiki_editor', 'Kaiki', 'Kano', 'kaiki_editor@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, 'editor', '2024-12-01 22:59:50'),
(4, 'Yun_admin', 'Yun', 'Yun', 'Yun_admin@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, 'admin', '2024-12-01 22:59:50'),
(6, 'Yun_viewer', 'Yun', 'Yun', 'Yun_viewer@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, 'viewer', '2024-12-01 22:59:50'),
(7, 'Joy_admin', 'Joy', 'Joy', 'Joy_admin@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, 'admin', '2024-12-01 22:59:50'),
(8, 'Joy_editor', 'Joy', 'Joy', 'Joy_editor@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, 'editor', '2024-12-01 22:59:50'),
(9, 'Joy_viewer', 'Joy', 'Joy', 'Joy_viewer@example.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', NULL, 'viewer', '2024-12-01 22:59:50'),
(10, 'new_user', 'New', 'User', 'new_user@example.com', '$2y$10$pHpOmpp9JC/i/cOXkAMunuMZHwXAZOOWkZYXV.7Xnywl9BNHD8qzu', NULL, 'viewer', '2024-12-02 05:14:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auditrecord`
--
ALTER TABLE `auditrecord`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingredient`
--
ALTER TABLE `ingredient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `liked`
--
ALTER TABLE `liked`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `recipe_categories`
--
ALTER TABLE `recipe_categories`
  ADD PRIMARY KEY (`recipe_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auditrecord`
--
ALTER TABLE `auditrecord`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ingredient`
--
ALTER TABLE `ingredient`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `liked`
--
ALTER TABLE `liked`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ingredient`
--
ALTER TABLE `ingredient`
  ADD CONSTRAINT `ingredient_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`id`);

--
-- Constraints for table `recipe`
--
ALTER TABLE `recipe`
  ADD CONSTRAINT `recipe_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `recipe_categories`
--
ALTER TABLE `recipe_categories`
  ADD CONSTRAINT `recipe_categories_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`id`),
  ADD CONSTRAINT `recipe_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
