<?php
	// TOKEN
	define("API_KEY",			              "/* your telegram token here */ ");
	define("ROUTE", 			              "/* host route */ ");

	// DATABASE CONNECTION
	define("DB_HOST",                   "/* host */ ");
	define("DB_USERNAME",               "/* username */ ");
	define("DB_PASSWORD",               "/* password */ ");
	define("DB_NAME",                   "/* schema name */ ");

	// URL
	define("URL",                       "https://api.telegram.org/bot");
	define("URLKEY",                    URL .API_KEY);

	// METHODS
	define("SET_WEBHOOK",               URLKEY ."/setWebhook?url=" .ROUTE);
	define("DELETE_WEBHOOK",            URLKEY ."/deleteWebhook");
	define("GET_UPDATES",               URLKEY ."/getUpdates");
	define("SEND_MESSAGE",              URLKEY ."/sendMessage");
?>