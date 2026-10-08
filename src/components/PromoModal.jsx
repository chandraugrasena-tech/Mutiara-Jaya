import { useState } from "react";
import { useCart } from "../utils/CartContext";
import { rp } from "../utils/promos";

export default function PromoModal({ onClose }) {
  const { promos, promoId, selectPromo, subtotal } = useCart();
  const [code, setCode] = useState("");
  const [error, setError] = useState("");

  const handleToggle = (promo) => {
    selectPromo(promo.id === promoId ? null : promo.id);
    onClose();
  };

  const handleCode = () => {
    const found = promos.find((p) => p.id === code.trim().toUpperCase());
    if (!found) return setError("Kode promo tidak ditemukan.");
    if (subtotal < found.minOrder) {
      return setError(`Belanja ${rp(found.minOrder - subtotal)} lagi untuk memakai promo ini.`);
    }
    selectPromo(found.id);
    onClose();
  };

  return (
    <div
      className="fixed inset-0 bg-black/60 z-[100] flex items-end sm:items-center justify-center sm:p-4"
      onClick={onClose}
    >
      <div
        className="bg-white text-gray-800 w-full max-w-md rounded-t-3xl sm:rounded-2xl p-5 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="flex justify-between items-center border-b pb-3">
          <h3 className="font-extrabold text-base text-gray-900">🏷️ Pilih promo</h3>
          <button
            type="button"
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer"
            aria-label="Tutup"
          >
            ✕
          </button>
        </div>

        <div className="space-y-1.5">
          <div className="flex gap-2">
            <input
              type="text"
              value={code}
              onChange={(e) => {
                setCode(e.target.value);
                setError("");
              }}
              placeholder="Punya kode promo? Ketik di sini"
              className="flex-1 border border-gray-300 rounded-xl px-3 py-2 text-xs uppercase focus:outline-none focus:border-[#165823]"
            />
            <button
              type="button"
              onClick={handleCode}
              disabled={!code.trim()}
              className="bg-[#165823] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-[#12481c] disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
            >
              Pakai
            </button>
          </div>
          {error && <p className="text-[11px] text-red-500 font-semibold">{error}</p>}
        </div>

        <div className="space-y-2">
          {promos.map((promo) => {
            const eligible = subtotal >= promo.minOrder;
            const isSelected = promoId === promo.id && eligible;
            return (
              <div
                key={promo.id}
                onClick={() => eligible && handleToggle(promo)}
                className={`p-3 rounded-xl border flex justify-between items-center gap-3 transition-all ${
                  !eligible
                    ? "opacity-60 bg-gray-50 cursor-not-allowed"
                    : isSelected
                    ? "border-[#165823] bg-green-50/60 cursor-pointer"
                    : "border-gray-200 hover:border-gray-300 cursor-pointer"
                }`}
              >
                <div>
                  <p className="font-bold text-xs text-gray-800">{promo.title}</p>
                  <p className="text-[10px] text-gray-500">{promo.desc}</p>
                  <p className="text-[10px] text-gray-500">
                    Min. belanja {rp(promo.minOrder)}
                    {!eligible && (
                      <span className="text-red-500 font-semibold">
                        {" "}
                        • kurang {rp(promo.minOrder - subtotal)}
                      </span>
                    )}
                  </p>
                </div>
                <span
                  className={`text-xs font-bold px-3 py-1 rounded-lg shrink-0 ${
                    isSelected ? "bg-[#165823] text-white" : "bg-gray-100 text-gray-700"
                  }`}
                >
                  {isSelected ? "Lepas" : "Gunakan"}
                </span>
              </div>
            );
          })}
        </div>

        {promoId && (
          <button
            type="button"
            onClick={() => {
              selectPromo(null);
              onClose();
            }}
            className="w-full py-2.5 rounded-xl text-xs font-bold border border-gray-300 text-gray-600 hover:bg-gray-50 cursor-pointer"
          >
            Tidak pakai promo
          </button>
        )}
      </div>
    </div>
  );
}
