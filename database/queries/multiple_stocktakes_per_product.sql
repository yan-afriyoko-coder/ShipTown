SELECT products.sku, warehouses.code, count(*) as times_counted

FROM `inventory_movements`

         LEFT JOIN warehouses on warehouses.id = warehouse_id
         LEFT JOIN products on products.id = product_id

WHERE `description` = 'stocktake'
  AND products.sku != '45'
  AND warehouses.code not in ('TRA','100')

  AND inventory_movements.created_at > '01 Dec 2022'

GROUP BY inventory_id

HAVING count(*) > 1

ORDER BY count(*) DESC

LIMIT 100
