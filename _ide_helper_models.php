<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Configuration
 *
 * @method static Configuration firstOrNew(array $array)
 * @method static Configuration where(string $string, $key)
 * @method firstOrFail()
 * @property mixed value
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Configuration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Configuration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Configuration query()
 * @method static \Illuminate\Database\Eloquent\Builder|Configuration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Configuration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Configuration whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Configuration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Configuration whereValue($value)
 */
	class Configuration extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Inventory
 *
 * @property float quantity
 * @property float quantity_reserved
 * @property int $id
 * @property int|null $warehouse_id
 * @property int $product_id
 * @property int $location_id
 * @property string $shelve_location
 * @property string $quantity
 * @property string $quantity_reserved
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $quantity_available
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory query()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereQuantityReserved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereShelveLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereWarehouseId($value)
 */
	class Inventory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Invite
 *
 * @property int $id
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Invite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereUpdatedAt($value)
 */
	class Invite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property BigInteger id
 * @property BigInteger shipping_address_id
 * @property string order_number
 * @property string status_code
 * @property integer product_line_count
 * @property integer total_quantity_ordered
 * @property Carbon|null picked_at
 * @method self isPicked(bool $expected)
 * @method self whereIsPicked()
 * @method self whereIsNotPicked()
 * @property Carbon|null packed_at
 * @property Carbon|null deleted_at
 * @property Carbon|null updated_at
 * @property Carbon|null created_at
 * @property int $id
 * @property string $order_number
 * @property string $shipping_number
 * @property int|null $shipping_address_id
 * @property string|null $order_placed_at
 * @property string|null $order_closed_at
 * @property int $product_line_count
 * @property float $total_quantity_ordered
 * @property string|null $status_code
 * @property int $is_picked
 * @property string|null $picked_at
 * @property string|null $packed_at
 * @property int|null $packer_user_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $raw_import
 * @property mixed $is_packed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderProduct[] $orderProducts
 * @property-read int|null $order_products_count
 * @property-read \App\User|null $packer
 * @property-read \App\Models\OrderAddress|null $shippingAddress
 * @method static \Illuminate\Database\Eloquent\Builder|Order active()
 * @method static \Illuminate\Database\Eloquent\Builder|Order isPacked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order packlist($inventory_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderPlacedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePackedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePackerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePickedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProductLineCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRawImport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotalQuantityOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderAddress
 *
 * @property mixed id
 * @property int $id
 * @property string $company
 * @property string $gender
 * @property string $first_name
 * @property string $last_name
 * @property string $address1
 * @property string $address2
 * @property string $postcode
 * @property string $city
 * @property string $state_code
 * @property string $state_name
 * @property string $country_code
 * @property string $country_name
 * @property string $phone
 * @property string $fax
 * @property string $website
 * @property string $region
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereStateCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereStateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderAddress whereWebsite($value)
 */
	class OrderAddress extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderProduct
 *
 * @property BigInteger order_id
 * @property BigInteger|null product_id
 * @property string|null sku_ordered
 * @property string|null name_ordered
 * @property float quantity_ordered
 * @property float quantity_picked
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property string $sku_ordered
 * @property string $name_ordered
 * @property string|null $price
 * @property string $quantity_ordered
 * @property string $quantity_picked
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct addInventorySource($inventory_location_id)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereNameOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQuantityOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQuantityPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereSkuOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrderProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderProduct withoutTrashed()
 */
	class OrderProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Packlist
 *
 * @property Carbon|null packed_at
 * @property int order_id
 * @package App\Models
 * @property int $id
 * @property int|null $order_id
 * @property int|null $order_product_id
 * @property int|null $product_id
 * @property string $location_id
 * @property string $sku_ordered
 * @property string $name_ordered
 * @property string $quantity_requested
 * @property string $quantity_packed
 * @property int|null $packer_user_id
 * @property string|null $packed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property mixed $is_packed
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist addInventorySource($inventory_location_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereNameOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereOrderProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist wherePackedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist wherePackerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereQuantityPacked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereQuantityRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereSkuOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Packlist whereUpdatedAt($value)
 */
	class Packlist extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Pick
 *
 * @property string sku_ordered
 * @property string name_ordered
 * @property float quantity_required
 * @property DateTime|null picked_at
 * @property bool is_picked
 * @property User user
 * @method static self whereHasQuantityRequired()
 * @property int $id
 * @property int|null $product_id
 * @property string $sku_ordered
 * @property string $name_ordered
 * @property string $quantity_required
 * @property int|null $picker_user_id
 * @property string|null $picked_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $is_picked
 * @property-read \App\Models\Product|null $product
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Pick addInventorySource($inventory_location_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick minimumShelfLocation($currentLocation)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pick newQuery()
 * @method static \Illuminate\Database\Query\Builder|Pick onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Pick query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereInStock($in_stock)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereNameOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereNotPicked()
 * @method static \Illuminate\Database\Eloquent\Builder|Pick wherePicked()
 * @method static \Illuminate\Database\Eloquent\Builder|Pick wherePickedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick wherePickerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereQuantityRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereSkuOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pick whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Pick withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Pick withoutTrashed()
 */
	class Pick extends \Eloquent {}
}

namespace App\Models{
/**
 * Class PickRequest
 *
 * @property BigInteger pick_id
 * @property float quantity_required
 * @property float quantity_picked
 * @package App\Models
 * @property int $id
 * @property int|null $order_id
 * @property int $order_product_id
 * @property string $quantity_required
 * @property string $quantity_picked
 * @property int|null $pick_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OrderProduct $orderProduct
 * @property-read \App\Models\Pick|null $pick
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest newQuery()
 * @method static \Illuminate\Database\Query\Builder|PickRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereOrderProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest wherePickId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereQuantityPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereQuantityRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PickRequest withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PickRequest withoutTrashed()
 */
	class PickRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Picklist
 *
 * @property float quantity_picked
 * @property DateTime|null picked_at
 * @property Order|null order
 * @property int $id
 * @property int|null $order_id
 * @property int|null $order_product_id
 * @property int|null $product_id
 * @property string $sku_ordered
 * @property string $name_ordered
 * @property string $quantity_requested
 * @property string $quantity_picked
 * @property int|null $picker_user_id
 * @property string|null $picked_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $is_picked
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Inventory[] $inventory
 * @property-read int|null $inventory_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist inStockOnly($in_stock_only)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist inventorySource($inventory_location_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist minimumShelfLocation($currentLocation)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist newQuery()
 * @method static \Illuminate\Database\Query\Builder|Picklist onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereIsSingleLineOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereNameOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereOrderProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist wherePickedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist wherePickerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereQuantityPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereQuantityRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereSkuOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picklist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Picklist withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Picklist withoutTrashed()
 */
	class Picklist extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Product
 *
 * @property BigInteger id
 * @property string sku
 * @property string name
 * @property double price
 * @property double sale_price
 * @property DateTime sale_price_start_date
 * @property DateTime sale_price_end_date
 * @property double quantity
 * @property double quantity_reserved
 * @method static updateOrCreate(array $array, array $attributes)
 * @method static whereHasText(string $text)
 * @property int $id
 * @property string $sku
 * @property string $name
 * @property string $price
 * @property string $sale_price
 * @property \Illuminate\Support\Carbon $sale_price_start_date
 * @property \Illuminate\Support\Carbon $sale_price_end_date
 * @property string $quantity
 * @property string $quantity_reserved
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductAlias[] $aliases
 * @property-read int|null $aliases_count
 * @property-read mixed $quantity_available
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Inventory[] $inventory
 * @property-read int|null $inventory_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product addInventorySource($inventory_location_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product skuOrAlias($skuOrAlias)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQuantityReserved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSalePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSalePriceEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSalePriceStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductAlias
 *
 * @property int $id
 * @property int $product_id
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAlias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAlias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAlias query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAlias whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAlias whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAlias whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAlias whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAlias whereUpdatedAt($value)
 */
	class ProductAlias extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RmsapiConnection
 *
 * @property BigInteger id
 * @property BigInteger location_id
 * @property int $id
 * @property string $location_id
 * @property string $url
 * @property string $username
 * @property string $password
 * @property int $products_last_timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection query()
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection whereProductsLastTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiConnection whereUsername($value)
 */
	class RmsapiConnection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RmsapiProductImport
 *
 * @property array raw_import
 * @property BigInteger connection_id
 * @property int $id
 * @property int $connection_id
 * @property string|null $batch_uuid
 * @property string|null $when_processed
 * @property int|null $product_id
 * @property string|null $sku
 * @property array $raw_import
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport query()
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereBatchUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereConnectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereRawImport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RmsapiProductImport whereWhenProcessed($value)
 */
	class RmsapiProductImport extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserConfiguration
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserConfiguration query()
 */
	class UserConfiguration extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Warehouse
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUpdatedAt($value)
 */
	class Warehouse extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Widget
 *
 * @property int $id
 * @property string $name
 * @property array $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Widget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget query()
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Widget whereUpdatedAt($value)
 */
	class Widget extends \Eloquent {}
}

namespace App\Modules\Api2cart\src\Models{
/**
 * App\Modules\Api2cart\src\Models\Api2cartConnection
 *
 * @property int $id
 * @property string $location_id
 * @property string $type
 * @property string $url
 * @property string $prefix
 * @property string|null $bridge_api_key
 * @property string $last_synced_modified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection query()
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection whereBridgeApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection whereLastSyncedModifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartConnection whereUrl($value)
 */
	class Api2cartConnection extends \Eloquent {}
}

namespace App\Modules\Api2cart\src\Models{
/**
 * App\Modules\Api2cart\src\Models\Api2cartOrderImports
 *
 * @property array raw_import
 * @property int $id
 * @property int|null $connection_id
 * @property int|null $order_id
 * @property string|null $when_processed
 * @property string|null $order_number
 * @property array $raw_import
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports query()
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports whereConnectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports whereRawImport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api2cartOrderImports whereWhenProcessed($value)
 */
	class Api2cartOrderImports extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property BigInteger printer_id
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $printer_id
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePrinterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

