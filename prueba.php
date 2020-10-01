<?php
        try {
            $db = new PDO("informix:host=10.254.4.225; service=1977;database=siu_guarani; server=ol_guarani; protocol=onsoctcp;EnableScrollableCursors=1;", "apache", "WpXyjYLXkpJRJGvqmGu4");
            print "Hello World!</br></br>";
            print "Connection Established!</br></br>";
            $stmt = $db->query("select * from sga_personas");
            $res = $stmt->fetch( PDO::FETCH_BOTH );
            $rows = $res[0];
            echo "Table contents: $rows.</br>";
            } catch (PDOException $e) {
                print $e->getMessage();
           } 

