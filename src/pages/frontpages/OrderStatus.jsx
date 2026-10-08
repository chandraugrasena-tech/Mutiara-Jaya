import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { useCart } from "../../utils/CartContext";
import { rp } from "../../utils/promos";

const STEPS = [
  { icon: "✅", title: "Pesanan diterima", desc: "Mutiara Jaya sudah menerima pesananmu" },
  { icon: "👨‍🍳", title: "Sedang dimasak", desc: "Dapur menyiapkan makananmu" },
  { icon: "🛵", title: "Driver menuju lokasimu", desc: "Pesanan sedang diantar" },
  { icon: "🎉", title: "Pesanan tiba", desc: "Selamat menikmati!" },
];
const STEP_MS = 8000;

export default function OrderStatus() {
  const { id } = useParams();
  const { orders } = useCart();
  const order = orders.find((o) => o.id === id);
  const [now, setNow] = useState(Date.now());

  useEffect(() => {
    const t = setInterval(() => setNow(Date.now()), 1000);
    return () => clearInterval(t);
  }, []);

  if (!order) {
    return (
      <div className="max-w-md mx-auto text-center py-16 space-y-3">
        <div className="text-5xl">🔎</div>
        <h2 className="text-lg font-bold text-gray-800">Pesanan tidak ditemukan</h2>
        <Link to="/" className="inline-block text-sm font-bold text-[#165823] underline">
          Kembali ke beranda
        </Link>
      </div>
    );
  }

  const step = Math.min(STEPS.length - 1, Math.floor((now - order.createdAt) / STEP_MS));
  const done = step === STEPS.length - 1;

  return (
    <div className="max-w-2xl mx-auto space-y-5">
      <div className="bg-gradient-to-r from-[#165823] to-[#218335] text-white p-5 rounded-3xl">
        <p className="text-xs text-[#ffdada]">Pesanan #{order.id}</p>
        <h2 className="text-xl font-black mt-1">
          {STEPS[step].icon} {STEPS[step].title}
        </h2>
        <p className="text-xs text-[#ffdada] mt-1">{STEPS[step].desc}</p>
      </div>

      <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-4">
        {STEPS.map((s, i) => {
          const reached = i <= step;
          return (
            <div key={s.title} className="flex items-start gap-3">
              <div
                className={`w-7 h-7 shrink-0 rounded-full flex items-center justify-center text-xs font-bold ${
                  reached ? "bg-[#165823] text-white" : "bg-gray-100 text-gray-400"
                }`}
              >
                {reached ? "✓" : i + 1}
              </div>
              <div>
                <p className={`text-sm font-bold ${reached ? "text-gray-800" : "text-gray-400"}`}>{s.title}</p>
                <p className="text-xs text-gray-500">{s.desc}</p>
              </div>
            </div>
          );
        })}
      </div>

      <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-2 text-xs text-gray-700">
        <p>
          <strong>📍 Diantar ke:</strong> {order.address ? `${order.address.label} • ${order.address.detail}` : "-"}
        </p>
        <p>
          <strong>💳 Pembayaran:</strong> {order.paymentMethod}
        </p>
        {order.notes && (
          <p>
            <strong>📝 Catatan:</strong> {order.notes}
          </p>
        )}
        <hr />
        {order.items.map((i) => (
          <div key={i.id} className="flex justify-between">
            <span>
              {i.quantity}x {i.name}
            </span>
            <span>{rp(i.price * i.quantity)}</span>
          </div>
        ))}
        <hr />
        <div className="flex justify-between">
          <span>Subtotal</span>
          <span>{rp(order.subtotal)}</span>
        </div>
        <div className="flex justify-between">
          <span>Ongkir</span>
          <span>{rp(order.ongkir)}</span>
        </div>
        {order.discount > 0 && (
          <div className="flex justify-between text-green-700 font-semibold">
            <span>Promo {order.promoTitle ? `(${order.promoTitle})` : ""}</span>
            <span>- {rp(order.discount)}</span>
          </div>
        )}
        <div className="flex justify-between font-extrabold text-sm text-gray-900 pt-1">
          <span>Total</span>
          <span>{rp(order.total)}</span>
        </div>
      </div>

      <Link
        to="/"
        className={`block text-center py-3 rounded-xl text-sm font-bold ${
          done ? "bg-[#165823] text-white hover:bg-[#12481c]" : "border border-[#165823] text-[#165823] hover:bg-green-50"
        }`}
      >
        {done ? "Pesan lagi" : "Kembali ke beranda"}
      </Link>
    </div>
  );
}
