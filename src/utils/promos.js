export const PROMOS = [
  {
    id: "ONGKIR10",
    title: "Gratis Ongkir Rp 10.000",
    desc: "Bebas ongkir ke semua area antar",
    minOrder: 30000,
    type: "ongkir",
    value: 10000,
  },
  {
    id: "DISKON5",
    title: "Diskon Makanan Rp 5.000",
    desc: "Potongan langsung untuk total makanan",
    minOrder: 25000,
    type: "diskon",
    value: 5000,
  },
  {
    id: "HEMAT10",
    title: "Diskon 10% (maks. Rp 8.000)",
    desc: "Hemat 10% dari total makanan",
    minOrder: 40000,
    type: "persen",
    value: 10,
    max: 8000,
  },
];

export const rp = (n) => "Rp " + Number(n || 0).toLocaleString("id-ID");

export function getDiscount(promo, subtotal, ongkir) {
  if (!promo || subtotal < promo.minOrder) return 0;
  if (promo.type === "ongkir") return Math.min(promo.value, ongkir);
  if (promo.type === "diskon") return Math.min(promo.value, subtotal);
  if (promo.type === "persen") {
    return Math.min(Math.round((subtotal * promo.value) / 100), promo.max ?? Infinity);
  }
  return 0;
}