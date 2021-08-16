<?php

namespace App\Models\AWSBilling;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AwsBilling extends Model
{
    protected $connection = 'aws-billing';

    protected $table = 'awsbilling';

    public function changeTable(string $time) : void
    {
        $this->table = 'awsbilling'.$time;
    }

    public function getUniqueProductsAsArray() : array
    {
        $results = [];

        $records = $this->select(DB::raw('DISTINCT lineitem_productcode as product'))
            ->orderBy('lineitem_productcode', 'ASC')
            ->get();

        if(count($records) > 0)
        {
            foreach ($records as $record)
            {
                $results[] = $record->product;
            }
        }

        return $results;
    }

    public function getAllRecordsByDate(string $date)
    {
        $results = [];

        $records = $this->where('lineitem_usagestartdate', 'LIKE', "%$date%")
            ->orderBy('identity_timeinterval', 'ASC')
            ->get();

        if(count($records) > 0)
        {
            $results = $records->toArray();
        }

        return $results;
    }

    public function getAllRecordsByProduct(string $product, bool $cursor = false)
    {
        $results = [];

        $records = $this->where('lineitem_productcode', '=', $product);

        if($cursor)
        {
            $records = $records->cursor();
        }
        else
        {
            $records = $records->get();
        }

        if(count($records) > 0)
        {
            $results = $records->toArray();
        }

        return $results;
    }

    public function getAllRecordsByProductAndDate(string $product, string $date)
    {
        $results = [];

        $records = $this->where('lineitem_productcode', '=', $product)
            ->where('lineitem_usagestartdate', 'LIKE', "%$date%")
            ->orderBy('identity_timeinterval', 'ASC')
            ->get();

        if(count($records) > 0)
        {
            $results = $records->toArray();
        }

        return $results;
    }
}
