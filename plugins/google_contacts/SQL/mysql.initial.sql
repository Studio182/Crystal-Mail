CREATE TABLE google_contacts LIKE contacts;

ALTER TABLE `google_contacts`
  ADD CONSTRAINT `google_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
