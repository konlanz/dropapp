<?php

    $table = 'stock';
    $action = 'stock_overview';
    $ajax = checkajax();

    if (!$ajax) {
        initlist();

        $cmsmain->assign('title', 'Stock Overview');

        listsetting('allowcopy', false);
        listsetting('allowadd', false);
        listsetting('allowdelete', false);
        listsetting('allowselect', false);
        listsetting('allowselectall', false);
        listsetting('allowsort', false);
        listsetting('allowmove', false);
        listsetting('allowcollapse', true);
        listsetting('listrownotclickable', true);

        $outgoinglocations = db_simplearray('SELECT id AS value, label FROM locations WHERE deleted IS NULL AND NOT visible AND NOT is_lost AND camp_id = '.$_SESSION['camp']['id'].' ORDER BY seq');
        $statusarray = ['in_stock' => 'In stock', 'ordered' => 'Ordered', 'untouched' => 'Untouched for 3 months', 'lost' => 'Lost'];
        listfilter(['label' => 'Boxes', 'options' => ($statusarray + $outgoinglocations)]);

        $genders = db_simplearray('SELECT id AS value, label FROM genders ORDER BY seq');
        listfilter2(['label' => 'Gender', 'options' => $genders]);
        listsetting('filter2cssclass', 'overview-filter-gender');

        listfilter3(['label' => 'By location', 'query' => 'SELECT id AS value, label FROM locations WHERE deleted IS NULL AND visible=1 AND camp_id = '.$_SESSION['camp']['id'].' ORDER BY seq']);
        listsetting('filter3cssclass', 'overview-filter-locations');

        addcolumn('text', 'Category', 'label');
        addcolumn('text', 'Subtypes', 'subtypes');
        addcolumn('text', 'Items', 'N_items');
        addcolumn('text', 'Locations', 'num_locations');

        $joinquery = 'SELECT
                a.*,
                IF(isnull(a.location),IF(isnull(a.Gender),IF(isnull(a.prod_id),"Category","Product"),"Gender"),"Size") as labelname,
                IF(isnull(a.location),IF(isnull(a.Gender),IF(isnull(a.prod_id),a.Category,a.Product),a.Gender),a.size) as label,
                TRIM(trailing "-" from concat(IF(isnull(a.cat_id),"",a.cat_id),"-",if(isnull(a.prod_id),"",a.prod_id),"-",if(isnull(a.g_id),"",a.g_id),"-",if(isnull(a.size_id),"",a.size_id),"-",if(isnull(a.loc_id),"",a.loc_id))) as new_id
            FROM
                (SELECT 
                    agrouping.*,
                    IF(ISNULL(agrouping.cat_id),1,0) + IF(ISNULL(agrouping.prod_id),1,0) + IF(ISNULL(agrouping.g_id),1,0) + IF(ISNULL(agrouping.loc_id),1,0) AS "level" 
                FROM
                    (SELECT 
                        pc.label As "Category",
                        pc.id as "cat_id", 
                        p.name As "Product",
                        p.group_id as "prod_id", 
                        g.label As "Gender",
                        g.id as "g_id", 
                        sizes.label as "size", 
                        sizes.id as "size_id",
                        locations.label as "location",
                        locations.id as "loc_id", 
                        count(stock.id) as "N_Boxes", 
                        sum(stock.items) as "N_items"
                    FROM 
                        product_categories as pc
                    INNER JOIN
                        (SELECT 
                            prod_a.group_id as group_id, 
                            prod_a.name as group_name,
                            prod_b.id as id,
                            prod_b.name as name,
                            prod_b.category_id as category_id,
                            prod_b.gender_id as gender_id
                        FROM
                            (SELECT 
                                min(a.id) as group_id,
                                upper(a.name) as name 
                            FROM 
                                products as a 
                            INNER JOIN
                                products as b ON upper(a.name)=upper(b.name) 
                            WHERE 
                                a.camp_id = :camp_id and b.camp_id = :camp_id and a.id<=b.id 
                            GROUP BY 
                                upper(a.name)
                            ) prod_a 
                        LEFT JOIN 
                            products as prod_b ON prod_a.name = upper(prod_b.name)
                        ) as p ON pc.id = p.category_id
                    INNER JOIN
                        genders as g ON p.gender_id = g.id 
                    INNER JOIN 
                        stock ON p.id = stock.product_id 
                    INNER JOIN
                        sizes ON stock.size_id = sizes.id 
                    INNER JOIN
                        locations on stock.location_id = locations.id 
                    WHERE 
                        locations.camp_id = :camp_id 
                        AND (NOT stock.deleted OR stock.deleted IS NULL)'.
                        ($_SESSION['filter2']['stock_overview'] ? ' AND (g.id = '.intval($_SESSION['filter2']['stock_overview']).')' : '')
                        .($_SESSION['filter3']['stock_overview'] ? ' AND (locations.id = '.intval($_SESSION['filter3']['stock_overview']).')' : '')
                        .('lost' == $_SESSION['filter']['stock_overview'] ? 'AND locations.is_lost=1' :
                            ('ordered' == $_SESSION['filter']['stock_overview'] ? 'AND (stock.ordered OR stock.picked) AND locations.visible' :
                                ('untouched' == $_SESSION['filter']['stock_overview'] ? 'AND DATEDIFF(now(),stock.modified) > 90 AND locations.visible' :
                                    (is_numeric($_SESSION['filter']['stock_overview']) ? ' AND (locations.id = '.intval($_SESSION['filter']['stock_overview']).')' : '
                                        AND locations.visible')))).
                    ' GROUP BY 
                        pc.label,pc.id,p.name,p.group_id,g.label,g.id,sizes.label,sizes.id,locations.label,locations.id WITH ROLLUP 
                    ) as agrouping
                WHERE
                    IF(ISNULL(agrouping.size_id),1,0) = IF(ISNULL(agrouping.loc_id),1,0)
                ) as a 
            WHERE 
                isnull(a.Category)=isnull(a.cat_id) and isnull(a.Product)=isnull(a.prod_id) and isnull(a.Gender)=isnull(a.g_id) and isnull(size)=isnull(size_id) and isnull(location)=isnull(loc_id)
            ';

        $rawdata = 'SELECT DISTINCTROW
                a1.labelname,
                a1.label,
                a1.new_id as id,
                a1.N_boxes,
                a1.N_items, 
                3-a1.level as level,
                a2.new_id as parent_id, 
                a1.location as location, 
                a1.size as size
            FROM
                ('.$joinquery.') AS a1
            INNER JOIN 
                ('.$joinquery.') AS a2
            ON 
                (INSTR(a1.new_id,concat(a2.new_id,"-"))=1 OR a2.new_id = "") and a1.level+1 = a2.level and (a2.level != 5)
            ORDER BY 
                id';

        $subtypes = 'SELECT 
                IF(isnull(raw_a.size),CONCAT(COUNT(DISTINCT raw_b.label)," ",raw_b.labelname,IF(COUNT( DISTINCT raw_b.label)>1,"s","")),raw_b.size) as subtypes, 
                raw_a.id
            FROM
                ('.$rawdata.') AS raw_a 
            LEFT JOIN ('.$rawdata.') AS raw_b ON (raw_a.id=raw_b.parent_id)
            WHERE 
                raw_a.id = raw_b.parent_id OR raw_a.level = 3 
            GROUP BY 
                raw_a.id';

        $locations = 'SELECT 
                raw_a.id as id,
                IF(count(distinct raw_b.location)=1,raw_b.location,concat(count(distinct raw_b.location)," locations")) as num_locations
            FROM 
                ('.$rawdata.') AS raw_a 
            INNER JOIN 
                ('.$rawdata.') AS raw_b 
            ON 
                INSTR(raw_b.id,concat(raw_a.id,"-"))=1 AND raw_b.location IS NOT NULL 
            GROUP BY 
                raw_a.id';

        $data = db_array('SELECT 
                IF((counts.subtypes=0),counts.subtypes,counts.subtypes) as subtypes, 
                IF(ISNULL(complete.location),num_locations.num_locations,complete.location) as num_locations, 
                complete.*  
            FROM 
                ('.$rawdata.')as complete 
            LEFT JOIN
                ('.$subtypes.') AS counts ON complete.id=counts.id 
            LEFT JOIN
                ('.$locations.') AS num_locations
            ON 
                complete.id=num_locations.id
            ORDER BY 
                CAST(SUBSTRING_INDEX(complete.id, "-",1) AS SIGNED), 
                complete.id;', ['camp_id' => $_SESSION['camp']['id']]);

        // Add what rows are expanded and collapsed
        foreach ($data as &$row) {
            if (in_array($row['id'], $_SESSION['stock_overview'])) {
                $row['notCollapsed'] = true;
            }
        }
        $cmsmain->assign('data', $data);
        $cmsmain->assign('listconfig', $listconfig);
        $cmsmain->assign('listdata', $listdata);
        $cmsmain->assign('include', 'cms_list.tpl');
    } else {
        switch ($_POST['do']) {
            case 'collapse':
                $_SESSION['stock_overview'] = $_POST['ids'];

                break;
            case 'collapseall':
                unset($_SESSION['stock_overview']);

                break;
        }

        echo json_encode($return);
        die();
    }
