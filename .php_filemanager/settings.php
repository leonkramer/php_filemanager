<?php
/*
 * Copyright (C) 2013 Leon Kramer <mail@leonkramer.com>
 * 
 * This file is part of php_filemanager.
 *                                                                         
 * php_filemanager is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * php_filemanager is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with php_filemanager. If not, see <http://www.gnu.org/licenses/>.
 */



/*
 * The path to your php_filemanager download folder relative
 * to your domain's home directory.
 *
 * Example Settings:
 *
 *   example.com -> "/"
 *   example.com/files/ -> "/files/"
 */
define('URI_BASE', "/");


/*
 * The date and time format to be displayed for directories
 * and other files
 */
define('DATE_FORMAT', "d.m.Y H:i:s");

/*
 * After this amount of seconds without refresh, 
 * php_filemanager will ask for passwords again */
define('PASSWD_TIMEOUT', 180);
?>
