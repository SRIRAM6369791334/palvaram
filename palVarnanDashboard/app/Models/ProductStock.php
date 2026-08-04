<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductStock extends Model {
    use HasFactory;

    protected $fillable = ['productid', 'category_id', 'subcategory_id', 'pro_ver_id', 'productname', 'overallstock', 'availablestock', 'salestock', 'low_stocks', 'last_stockupdate_date',
                                'country_id', 'warehouse_id', 'branch_id'];


    protected $table = 'productstocks';

 

    public function product()
    {
        return $this->belongsTo(Product::class, 'productid');
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function branch()
    {
        return $this->belongsTo(WareHouseBranch::class, 'branch_id');
    }

    // ── Scopes ──

    public function scopeWithFilters($query, array $filters)
    {
        return $query
            ->when(!empty($filters['country_id']),   fn($q) => $q->where('country_id',   $filters['country_id']))
            ->when(!empty($filters['warehouse_id']), fn($q) => $q->where('warehouse_id', $filters['warehouse_id']))
            ->when(!empty($filters['branch_id']),    fn($q) => $q->where('branch_id',    $filters['branch_id']))
            ->when(!empty($filters['category_id']),  fn($q) => $q->where('category_id',  $filters['category_id']));
    }

    public function scopeLowStock($query)
    {
        return $query
            ->whereNotNull('low_stocks')
            ->whereColumn('availablestock', '<=', 'low_stocks');
    }

    public function scopeDeadStock($query)
    {
        $cutoff = Carbon::now()->subDays(30)->toDateString();

        return $query
            ->where('salestock', 0)
            ->where('overallstock', '>', 0)
            ->where('last_stockupdate_date', '<=', $cutoff)
            ->selectRaw('productstocks.*, DATEDIFF(NOW(), last_stockupdate_date) as days_stagnant');
    }


    public function Productvarient() {
        return $this->belongsTo( ProductVarient::class, 'id' );
    }
}