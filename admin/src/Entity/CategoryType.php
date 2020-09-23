<?php

namespace App\Entity;

class CategoryType
{
    const JUNK_REMOVAL = 'junk_removal';
    const RECYCLING = 'recycling';
    const SHREDDING = 'shredding';
    const DONATION = 'donation';
    const BUSYBEE = 'busybee';
    const MOVING = 'moving';
}

/*
select c.id, c.parent_id, c.lvl, c.ordering, ct.name 
from categories c ,
category_translations ct
where c.type = 'donation'
and c.id = ct.category_id
order by c.lvl, c.ordering
;
*/