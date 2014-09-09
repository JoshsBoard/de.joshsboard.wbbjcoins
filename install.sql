-- only single alter tables supported
ALTER TABLE wbb1_board ADD customJCoins TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE wbb1_board ADD customJCoinsCreateThread INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wbb1_board ADD customJCoinsCreatePost INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wbb1_board ADD customJCoinsTrashThread INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wbb1_board ADD customJCoinsTrashPost INT(10) NOT NULL DEFAULT 0;