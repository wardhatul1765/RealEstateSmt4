// resources/js/components/DashboardProperti.jsx
import React from "react";
import { Card, CardContent } from "@/components/ui/card";
import { BarChart, Bar, XAxis, YAxis, Tooltip, PieChart, Pie, Cell, ResponsiveContainer } from "recharts";

// Sample Data
const data = [
  { bathrooms: 3, bedrooms: 2, price: 2500000, furnishing: "NO", sizeMin: 1323, displayAddress: "Binghatti Canal, Business Bay" },
  { bathrooms: 3, bedrooms: 2, price: 7200000, furnishing: "YES", sizeMin: 1410, displayAddress: "La Vie, Jumeirah Beach Residence" }
];

export default function DashboardProperti() {
  return (
    <div className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <Card className="p-5">Jumlah Properti Terdaftar: {data.length} Unit</Card>
        <Card className="p-5">Jumlah Furnished YES: {data.filter(d => d.furnishing === "YES").length} Unit</Card>
        <Card className="p-5">Jumlah Furnished NO: {data.filter(d => d.furnishing === "NO").length} Unit</Card>
        <Card className="p-5">Harga Rata-rata: ${Math.round(data.reduce((sum, d) => sum + d.price, 0) / data.length)}</Card>
      </div>
    </div>
  );
}
