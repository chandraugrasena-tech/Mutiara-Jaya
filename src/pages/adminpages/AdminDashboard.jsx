import { Link } from "react-router-dom";
import { useCart } from "../../utils/CartContext";
import { products } from "../../utils/data";
import { rp } from "../../utils/promos";

export default function AdminDashboard() {
  const { orders } = useCart();

  const totalPendapatan = orders.reduce((sum, o) => sum + o.total, 0);

  const stats = [
    { icon: "🧾", label: "Jumlah Pesanan", value: orders.length },
    { icon: "💰", label: "Total Pendapatan", value: rp(totalPendapatan) },
    { icon: "🍽️", label: "Jumlah Menu", value: products.length },
  ];

  const getStatus = (order) => (Date.now() - order.createdAt > 24000 ? "Selesai" : "Diproses");

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Dashboard</h1>

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {stats.map((s) => (
          <div key={s.label} className="bg-white p-5 rounded shadow flex items-center gap-4">
            <div className="text-3xl">{s.icon}</div>
            <div>
              <p className="text-xs text-gray-500">{s.label}</p>
              <p className="text-xl font-extrabold text-gray-800">{s.value}</p>
            </div>
          </div>
        ))}
      </div>
      <div className="bg-white p-5 rounded shadow">
        <h2 className="font-bold mb-3">Daftar Pesanan Masuk</h2>

        {orders.length === 0 ? (
          <div className="text-sm text-gray-500 py-6 text-center">
            Belum ada pesanan. Buat pesanan dari halaman toko, nanti muncul di sini.
            <div className="mt-3">
              <Link to="/" className="font-bold text-[#165823] underline">
                Ke halaman toko
              </Link>
            </div>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left">
              <thead>
                <tr className="border-b text-gray-600">
                  <th className="py-2 pr-4">ID</th>
                  <th className="py-2 pr-4">Waktu</th>
                  <th className="py-2 pr-4">Alamat</th>
                  <th className="py-2 pr-4">Item</th>
                  <th className="py-2 pr-4">Pembayaran</th>
                  <th className="py-2 pr-4">Total</th>
                  <th className="py-2">Status</th>
                </tr>
              </thead>
              <tbody>
                {orders.map((o) => (
                  <tr key={o.id} className="border-b last:border-0">
                    <td className="py-2 pr-4 font-mono font-bold">{o.id}</td>
                    <td className="py-2 pr-4 whitespace-nowrap">
                      {new Date(o.createdAt).toLocaleString("id-ID")}
                    </td>
                    <td className="py-2 pr-4">{o.address ? o.address.label : "-"}</td>
                    <td className="py-2 pr-4">
                      {o.items.reduce((s, i) => s + i.quantity, 0)} porsi
                    </td>
                    <td className="py-2 pr-4">{o.paymentMethod}</td>
                    <td className="py-2 pr-4 font-bold">{rp(o.total)}</td>
                    <td className="py-2">
                      <span
                        className={`px-2 py-0.5 rounded-full text-xs font-bold ${
                          getStatus(o) === "Selesai"
                            ? "bg-green-100 text-green-700"
                            : "bg-yellow-100 text-yellow-700"
                        }`}
                      >
                        {getStatus(o)}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
