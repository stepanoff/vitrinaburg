<?php
class VForumHtmlHelper
{
	public function pager ($totalItems = 1, $itemsPerPage = 10, $urlArr, $currentPage = false, $max = 3, $title = false)
	{
        $totalPages = ceil($totalItems / $itemsPerPage);
        if ($totalPages <=1)
            return '';
        $res = '';
        $res .= '<div class="pages">';
        $res .= $title ? '<span>'.$title.'</span>' : '';
        for ($i=1; $i<=$totalPages; $i++)
        {
            if ($i>$max && $i != $totalPages)
                continue;

            if ($i == $currentPage)
                $res .= ' <b class="gradient1">'.$i.'</b>
';
            else
            {
                if ($i > 1)
                    $urlArr['page'] = $i;
                $res .= '
'.CHtml::link($i, $urlArr);
            }
            if ($i == $max && $totalPages > $max+1)
                $res .= ' <em>...</em>
';

        }
        $res .= '</div>';
        return $res;
	}
}
?>