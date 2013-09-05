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

class path
{
    public function say($path, $option = "KEEP_BASE")
    { 
        if ($option === "DROP_BASE") { 
            return "/" . current(preg_split("~^".URI_BASE."~", $path, 0, PREG_SPLIT_NO_EMPTY));
        }
        if ($option === "KEEP_BASE") {
            return $path;
        }
        return false;
    }

    public function current($path) {
        return substr($path, 0, strrpos($path, '/')+1);
    }
}

?>
