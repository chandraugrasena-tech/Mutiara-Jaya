import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useCart } from "../../utils/CartContext";
import { rp } from "../../utils/promos";

export default function Promo() {
  const navigate = useNavigate();
  const { promos, promoId, selectPromo, subtotal, cartItems } = useCart();
  const [notice, setNotice] = useState("");

  const handleUse = (promo) => {
    if (promoId === promo.id) {
      selectPromo(null);
      setNotice("");
      return;
    }
    selectPromo(promo.id);
    if (cartItems.length > 0) navigate("/cart");
    else setNotice(`Promo ${promo.id} dipilih. Pilih menu dulu, promo otomatis dipakai di keranjang.`);
  };

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div className="flex items-center justify-between border-b pb-3">
        <h2 className="text-xl font-bold text-gray-800">🎉 Promo & voucher</h2>
        <Link to="/" className="text-xs font-bold text-[#165823] hover:underline">
          ← Kembali ke menu
        </Link>
      </div>

      {notice && (
        <div className="bg-green-50 border border-green-200 text-green-800 text-xs font-semibold rounded-xl p-3 flex items-center justify-between gap-3">
          <span>{notice}</span>
          <Link to="/" className="font-bold underline shrink-0">
            Pilih menu
          </Link>
        </div>
      )}

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {promos.map((p) => {
          const used = promoId === p.id;
          const eligible = subtotal >= p.minOrder;
          return (
            <div
              key={p.id}
              className={`bg-white p-5 rounded-2xl border shadow-sm space-y-3 ${
                used ? "border-[#165823]" : "border-gray-100"
              }`}
            >
              <h3 className="font-bold text-base text-[#165823]">{p.title}</h3>
              <p className="text-xs text-gray-600">{p.desc}</p>
              <p className="text-[11px] text-gray-500">
                Min. belanja {rp(p.minOrder)}
                {cartItems.length > 0 && !eligible && (
                  <span className="text-red-500 font-semibold"> • kurang {rp(p.minOrder - subtotal)}</span>
                )}
              </p>
              <div className="flex items-center justify-between gap-2">
                <div className="bg-gray-100 px-2 py-1 rounded-lg text-xs font-mono font-bold text-gray-800 border">
                  {p.id}
                </div>
                <button
                  type="button"
                  onClick={() => handleUse(p)}
                  className={`text-xs font-bold px-4 py-2 rounded-xl transition-colors cursor-pointer ${
                    used ? "bg-gray-100 text-gray-700 hover:bg-gray-200" : "bg-[#165823] text-white hover:bg-[#12481c]"
                  }`}
                >
                  {used ? "Lepas promo" : "Pakai promo"}
                </button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
