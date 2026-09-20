<?php

function pagination($total, $page, $size = 20) {
    $pages = (int)ceil($total/$size);
 if ($pages < 2) return;

    echo '<nav class="pagination" aria-label="Strony wyników">';

    for ($i=max(1,$page-2); $i<=min($pages,$page+2); $i++) {
        $params = [];
        foreach ($_GET as $key => $value) {
            if (is_string($value)) {
                $params[$key] = $value;
            }
        }
 $params['page']=$i;

        echo '<a ' . ($i===$page ? 'aria-current="page" ' : '') . 'href="?' . e(http_build_query($params)) . '">' . $i . '</a>';

    } echo '</nav>';

}