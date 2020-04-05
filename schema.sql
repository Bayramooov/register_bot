CREATE TABLE `CANDIDATES` (
  `ID` int(32) NOT NULL,
  `NAME` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `AGE` int(3) NOT NULL,
  `REGION` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `SCHOOL` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `LEVEL` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PHONE` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `REG_DATE` int(32) NOT NULL,
  `CHAT_ID` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `USERS` (
  `CHAT_ID` int(32) NOT NULL,
  `FIRST_NAME` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `USERNAME` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `DATE` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `CANDIDATES`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `USER_ID` (`CHAT_ID`);

ALTER TABLE `USERS`
  ADD PRIMARY KEY (`CHAT_ID`);

ALTER TABLE `CANDIDATES`
  MODIFY `ID` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `CANDIDATES`
  ADD CONSTRAINT `CANDIDATES_ibfk_1` FOREIGN KEY (`CHAT_ID`) REFERENCES `USERS` (`CHAT_ID`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;