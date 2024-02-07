CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `publish_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE post_votes (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT(11) UNSIGNED NOT NULL,
  user_ip VARCHAR(50) NOT NULL,
  vote_type ENUM('like', 'dislike') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id)
)