<?php
/* @var $this yii\web\View */
?>
<h1>Процедура обновления</h1>

<h3>Первый раз:</h3>
<pre>
    <a href="/soft/Git-2.10.1-64-bit.exe">Download 64bit  GIT to htdocs</a>
    OR
    <a href="/soft/Git-2.10.1-32-bit.exe">Download 32bit  GIT to htdocs</a>
    EXECUTE and SETUP IT
    cd C:\xampp\htdocs\
    git clone https://github.com/abrikos/time_bonus
    cd time_bonus
    REM git reset  --hard origin/master
    git pull
    copy ..\time\db\database.db db\
    Open link below "Alter database" on local site
    OR
    #sqlite3 -init add-bonus.sql ..\db\database.db
</pre>

<a href="/update/pull" class="btn btn-primary">Pull Script changes</a>
<a href="/update/migrate" class="btn btn-primary">Alter database</a>
