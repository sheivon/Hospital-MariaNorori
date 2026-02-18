-- Add recipient_id column for private messages
ALTER TABLE chat_messages
  ADD COLUMN recipient_id INT NULL AFTER message;

-- Optional index for faster lookups in two-way conversations
CREATE INDEX idx_chat_recipient ON chat_messages (recipient_id);
