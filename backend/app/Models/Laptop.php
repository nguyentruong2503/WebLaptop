<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    use HasFactory;
    protected $fillable = [
    'CPU', 'GPU', 'GPU_type', 'HDMI_ports', 'LAN_port', 'RAM', 'SSD', 
    'Thunderbolt_ports', 'USB_A_ports', 'USB_C_ports', 'battery_capacity_wh',
    'charging_watt', 'des', 'dimensions', 'expandable_slots', 'jack_3_5mm',
    'screenSpecs', 'special_features', 'weight_kg'
];
    public function product()
{
    return $this->belongsTo(Product::class, 'productID', 'id');
}

}
