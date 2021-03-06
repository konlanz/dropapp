<?php

    $data['barcode'] = $_GET['barcode'];

    if ($_GET['barcode'] && !db_value('SELECT id FROM qr WHERE code = :code AND legacy = :legacy', ['code' => $_GET['barcode'], 'legacy' => (isset($_GET['qrlegacy']) ? 1 : 0)])) {
        // There is a barcode hash in the url, but this hash is not in the qr table
        $data['warning'] = true;
        $data['message'] = 'This is not a valid QR-code for '.$_SESSION['organisation']['label'];
        $data['barcode'] = '';
        // Check if it is a legacy error
        if (db_value('SELECT id FROM qr WHERE code = :code AND legacy = :legacy', ['code' => $_GET['barcode'], 'legacy' => (!isset($_GET['qrlegacy']) ? 1 : 0)])) {
            trigger_error('Scanned QR-code exist in qr-table, but with different legacy value!');
        } else {
            trigger_error($data['message']);
        }
    } else {
        // Load box data
        if ($_GET['boxid']) {
            // a boxid was passed through the url
            $box = db_row('SELECT s.*, c.id AS camp_id, c.name AS campname, CONCAT(p.name," ",g.label," ",IFNULL(s2.label, "")) AS product, p.name AS product2, g.label AS gender, IFNULL(s2.label, "") AS size, l.label AS location FROM stock AS s
                LEFT OUTER JOIN products AS p ON p.id = s.product_id
                LEFT OUTER JOIN genders AS g ON g.id = p.gender_id
                LEFT OUTER JOIN sizes AS s2 ON s2.id = s.size_id
                LEFT OUTER JOIN locations AS l ON l.id = s.location_id
                LEFT OUTER JOIN qr AS q ON q.id = s.qr_id
                LEFT OUTER JOIN camps AS c ON c.id = l.camp_id
                WHERE s.id = :id', ['id' => $_GET['boxid']]);
        } else {
            // a barcode hash was passed in the url and it exits in qr-table
            $qr_id = db_value('SELECT id FROM qr WHERE code = :code AND legacy = :legacy', ['code' => $_GET['barcode'], 'legacy' => (isset($_GET['qrlegacy']) ? 1 : 0)]);
            $box = db_row('SELECT s.*, c.id AS camp_id, c.name AS campname, CONCAT(p.name," ",g.label," ",IFNULL(s2.label, "")) AS product, p.name AS product2, g.label AS gender, IFNULL(s2.label, "") AS size, l.label AS location FROM stock AS s
                LEFT OUTER JOIN products AS p ON p.id = s.product_id
                LEFT OUTER JOIN genders AS g ON g.id = p.gender_id
                LEFT OUTER JOIN sizes AS s2 ON s2.id = s.size_id
                LEFT OUTER JOIN locations AS l ON l.id = s.location_id
                LEFT OUTER JOIN qr AS q ON q.id = s.qr_id
                LEFT OUTER JOIN camps AS c ON c.id = l.camp_id
                WHERE q.id = :qrid', ['qrid' => $qr_id]);
        }

        if ('0000-00-00 00:00:00' != $box['deleted'] && !is_null($box['deleted'])) {
            // Box is a deleted box
            trigger_error('Scanned box is a deleted box.');
            redirect('?editbox='.$box['id']);
        } elseif ($box['camp_id'] != $_SESSION['camp']['id'] && $box['campname']) {
            // Box is registered in a different camp
            trigger_error('Scanned box is registered to another base.');
            redirect('?editbox='.$box['id'].'&warning=true&message=Oops!! This box is registered in '.$box['campname'].', are you sure this is what you were looking for?<br /><br /> No? <a href="/mobile.php">Search again!</a><br /><br /> Yes? Edit and save this box to transfer it to '.$_SESSION['camp']['name'].'.');
        } else {
            // Box is not deleted and belongs to your base
            if ($box['id']) {
                // box is not empty
                $orders = db_value('SELECT COUNT(s.id) FROM stock AS s LEFT OUTER JOIN locations AS l ON s.location_id = l.id WHERE l.camp_id = :camp AND (NOT s.deleted OR s.deleted IS NULL) AND s.ordered', ['camp' => $_SESSION['camp']['id']]);
                $tpl->assign('orders', $orders);

                $locations = db_array('SELECT id AS value, label, IF(id = :location, true, false) AS selected FROM locations WHERE deleted IS NULL AND camp_id = :camp_id ORDER BY seq', ['camp_id' => $_SESSION['camp']['id'], 'location' => $box['location_id']]);
                $history = showHistory('stock', $box['id']);
                $tpl->assign('box', $box);
                $tpl->assign('history', $history);
                $tpl->assign('locations', $locations);
                $tpl->assign('include', 'mobile_scan.tpl');
            } else {
                // no box was loaded --> newbox
                redirect('?newbox='.$data['barcode'].(isset($_GET['qrlegacy']) ? '&qrlegacy=1' : ''));
            }
        }
    }
