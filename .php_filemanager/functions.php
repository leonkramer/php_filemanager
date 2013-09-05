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


function usort_dir($a, $b) {
    if ($a['type'] === "directory" && $b['type'] === "directory") {
        return ($a['href'] < $b['href'] ? -1 : 1);
    }
    if ($a['type'] !== "directory" && $b['type'] !== "directory") {
        return ($a['href'] < $b['href'] ? -1 : 1);
    }
    return ($a['type'] === "directory" ? -1 : 1);
}

?>
