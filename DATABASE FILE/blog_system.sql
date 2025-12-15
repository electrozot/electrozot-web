-- Blog System Tables

-- Create blog posts table
CREATE TABLE IF NOT EXISTS `tms_blog_posts` (
  `blog_id` int NOT NULL AUTO_INCREMENT,
  `blog_title` varchar(255) NOT NULL,
  `blog_slug` varchar(255) NOT NULL,
  `blog_content` longtext NOT NULL,
  `blog_excerpt` text,
  `blog_image` varchar(255) DEFAULT NULL,
  `blog_author_id` int NOT NULL,
  `blog_category` varchar(100) DEFAULT NULL,
  `blog_tags` varchar(255) DEFAULT NULL,
  `blog_status` enum('Draft','Published','Archived') DEFAULT 'Draft',
  `blog_views` int DEFAULT '0',
  `blog_seo_title` varchar(255) DEFAULT NULL,
  `blog_seo_description` text,
  `blog_seo_keywords` varchar(255) DEFAULT NULL,
  `blog_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `blog_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `blog_published_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`blog_id`),
  UNIQUE KEY `blog_slug` (`blog_slug`),
  KEY `blog_author_id` (`blog_author_id`),
  KEY `blog_status` (`blog_status`),
  KEY `blog_category` (`blog_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create blog categories table
CREATE TABLE IF NOT EXISTS `tms_blog_categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `category_slug` varchar(100) NOT NULL,
  `category_description` text,
  `category_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_slug` (`category_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO `tms_blog_categories` (`category_name`, `category_slug`, `category_description`) VALUES
('Electrical Tips', 'electrical-tips', 'Tips and guides for electrical maintenance and safety'),
('Home Maintenance', 'home-maintenance', 'General home maintenance advice'),
('DIY Guides', 'diy-guides', 'Do-it-yourself guides for simple repairs'),
('Safety Tips', 'safety-tips', 'Safety guidelines for home and electrical work'),
('Company News', 'company-news', 'Updates and news from Electrozot');
