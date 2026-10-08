import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useCart } from "../../utils/CartContext";
import { rp } from "../../utils/promos";
import AddressModal from "../../components/AddressModal";
import PromoModal from "../../components/PromoModal";

export default function Cart() {
  const navigate = useNavigate();
  const {
    cartItems,
    updateQuantity,
    removeFromCart,
    subtotal,
    ongkir,
    discount,
    total,
    selectedAddress,
    selectedPromo,
    activePromo,
    selectPromo,
    paymentMethod,
    setPaymentMethod,
    placeOrder,
  } = useCart();

  const [showAddress, setShowAddress] = useState(false);
  const [showPromo, setShowPromo] = useState(false);
  const [showQris, setShowQris] = useState(false);
  const [notes, setNotes] = useState("");

  const confirmOrder = () => {
    const id = placeOrder(notes);
    setShowQris(false);
    navigate(`/order/${id}`);
  };

  const handlePesan = () => {
    if (!selectedAddress) return setShowAddress(true);
    if (paymentMethod === "QRIS") setShowQris(true);
    else confirmOrder();
  };

  if (cartItems.length === 0) {
    return (
      <div className="max-w-2xl mx-auto p-12 text-center space-y-4">
        <div className="text-6xl">🛒</div>
        <h2 className="text-xl font-bold text-gray-800">Keranjang masih kosong</h2>
        <p className="text-xs text-gray-500">Pilih menu dulu dari halaman utama, nanti muncul di sini.</p>
        <Link
          to="/"
          className="inline-block bg-[#165823] text-white text-xs font-bold px-6 py-2.5 rounded-xl hover:bg-[#12481c]"
        >
          Pilih menu makanan
        </Link>
      </div>
    );
  }

  const promoShortfall = selectedPromo && !activePromo ? selectedPromo.minOrder - subtotal : 0;

  return (
    <div className="max-w-2xl mx-auto space-y-5">
      <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <div className="flex justify-between items-start gap-3">
          <div className="min-w-0">
            <h3 className="font-bold text-sm text-gray-800">📍 Anter ke</h3>
            {selectedAddress ? (
              <p className="text-xs text-gray-700 mt-1.5 break-words">
                <strong>{selectedAddress.label}</strong> • {selectedAddress.detail}
              </p>
            ) : (
              <p className="text-xs text-red-500 mt-1.5 font-semibold">Belum ada alamat pengiriman.</p>
            )}
          </div>
          <button
            type="button"
            onClick={() => setShowAddress(true)}
            className="text-xs font-bold text-[#165823] border border-[#165823] px-3 py-1.5 rounded-lg hover:bg-green-50 shrink-0 cursor-pointer"
          >
            Ganti
          </button>
        </div>
      </div>

      <div>
        <div className="flex justify-between items-center mb-2">
          <h2 className="text-xl font-bold text-gray-800">Pesanan kamu</h2>
          <Link to="/" className="text-xs font-bold text-[#165823] hover:underline">
            + Tambah menu
          </Link>
        </div>
        <div className="bg-white rounded-2xl border border-gray-100 p-4 divide-y divide-gray-100 shadow-sm">
          {cartItems.map((item) => (
            <div key={item.id} className="py-3 flex items-center gap-3 first:pt-0 last:pb-0">
              <Link to={`/product/${item.id}`}>
                <img src={item.img} alt={item.name} className="w-14 h-14 object-cover rounded-xl bg-gray-100" />
              </Link>
              <div className="flex-1 min-w-0">
                <h4 className="font-bold text-sm text-gray-800 truncate">{item.name}</h4>
                <p className="text-xs font-extrabold text-[#165823]">{rp(item.price * item.quantity)}</p>
              </div>
              <div className="flex items-center gap-2 border rounded-xl px-2 py-1 bg-gray-50">
                <button
                  type="button"
                  onClick={() => updateQuantity(item.id, -1)}
                  className="w-6 h-6 rounded-lg bg-white border font-bold text-xs hover:bg-gray-100 cursor-pointer"
                  aria-label="Kurangi"
                >
                  -
                </button>
                <span className="text-xs font-bold w-4 text-center">{item.quantity}</span>
                <button
                  type="button"
                  onClick={() => updateQuantity(item.id, 1)}
                  className="w-6 h-6 rounded-lg bg-white border font-bold text-xs hover:bg-gray-100 cursor-pointer"
                  aria-label="Tambah"
                >
                  +
                </button>
              </div>
              <button
                type="button"
                onClick={() => removeFromCart(item.id)}
                className="text-red-500 hover:text-red-700 text-xs font-bold px-1 cursor-pointer"
                aria-label="Hapus"
              >
                ✕
              </button>
            </div>
          ))}
        </div>
      </div>

      <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-2">
        <h3 className="font-bold text-sm text-gray-800">📝 Catatan untuk restoran</h3>
        <input
          type="text"
          value={notes}
          onChange={(e) => setNotes(e.target.value)}
          placeholder="Contoh: sambal dipisah ya"
          className="w-full border border-gray-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#165823]"
        />
      </div>
      <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <button
          type="button"
          onClick={() => setShowPromo(true)}
          className="w-full flex items-center justify-between gap-3 text-left cursor-pointer"
        >
          <div className="min-w-0">
            <h3 className="font-bold text-sm text-gray-800">🏷️ Promo</h3>
            {activePromo ? (
              <p className="text-xs text-green-700 font-semibold mt-1">
                {activePromo.title} • hemat {rp(discount)}
              </p>
            ) : promoShortfall > 0 ? (
              <p className="text-xs text-red-500 font-semibold mt-1">
                {selectedPromo.title} belum berlaku, belanja {rp(promoShortfall)} lagi
              </p>
            ) : (
              <p className="text-xs text-gray-500 mt-1">Pilih promo atau masukkan kode</p>
            )}
          </div>
          <span className="text-xs font-bold text-[#165823] shrink-0">{selectedPromo ? "Ganti" : "Pilih"} ›</span>
        </button>
        {selectedPromo && (
          <button
            type="button"
            onClick={() => selectPromo(null)}
            className="mt-2 text-[11px] font-bold text-red-500 hover:underline cursor-pointer"
          >
            Lepas promo
          </button>
        )}
      </div>

      <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-3">
        <h3 className="font-bold text-sm text-gray-800">💳 Metode pembayaran</h3>
        <div className="grid grid-cols-2 gap-3">
          {[
            { key: "CASH", icon: "💵", title: "CASH", sub: "Bayar di tempat" },
            { key: "QRIS", icon: "📱", title: "QRIS", sub: "Gopay, OVO, Shopee, BCA" },
          ].map((m) => {
            const on = paymentMethod === m.key;
            return (
              <button
                key={m.key}
                type="button"
                onClick={() => setPaymentMethod(m.key)}
                className={`p-3 rounded-xl border flex items-center gap-3 transition-all cursor-pointer ${
                  on ? "border-[#165823] bg-green-50/70 text-[#165823]" : "border-gray-200 hover:border-gray-300 text-gray-700"
                }`}
              >
                <div
                  className={`w-4 h-4 shrink-0 rounded-full border-2 flex items-center justify-center ${
                    on ? "border-[#165823]" : "border-gray-300"
                  }`}
                >
                  {on && <div className="w-2 h-2 rounded-full bg-[#165823]" />}
                </div>
                <div className="text-left">
                  <p className="text-xs font-bold">
                    {m.icon} {m.title}
                  </p>
                  <p className="text-[10px] text-gray-500 font-normal">{m.sub}</p>
                </div>
              </button>
            );
          })}
        </div>
      </div>
      <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-2 text-sm">
        <div className="flex justify-between text-gray-600 text-xs">
          <span>Subtotal</span>
          <span>{rp(subtotal)}</span>
        </div>
        <div className="flex justify-between text-gray-600 text-xs">
          <span>Ongkir</span>
          <span>{rp(ongkir)}</span>
        </div>
        {discount > 0 && (
          <div className="flex justify-between text-green-700 text-xs font-semibold">
            <span>Diskon promo</span>
            <span>- {rp(discount)}</span>
          </div>
        )}
        <hr className="my-2" />
        <div className="flex justify-between font-extrabold text-base text-gray-900">
          <span>Total</span>
          <span>{rp(total)}</span>
        </div>
        <button
          type="button"
          onClick={handlePesan}
          className="w-full mt-3 bg-[#165823] hover:bg-[#12481c] text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-md active:scale-95 cursor-pointer"
        >
          {paymentMethod === "QRIS" ? "Bayar dengan QRIS" : "Pesan sekarang"} • {rp(total)}
        </button>
      </div>

      {showAddress && <AddressModal onClose={() => setShowAddress(false)} />}
      {showPromo && <PromoModal onClose={() => setShowPromo(false)} />}

      {showQris && (
        <div className="fixed inset-0 bg-black/70 z-[100] flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl p-6 max-w-sm w-full text-center space-y-4 shadow-2xl">
            <h3 className="font-extrabold text-base text-gray-800">Scan QRIS untuk membayar</h3>
            <p className="text-xs text-gray-500">
              Total: <strong className="text-[#165823]">{rp(total)}</strong>
            </p>
            <div className="bg-gray-100 p-4 rounded-2xl inline-block border border-gray-200">
              <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=MUTIARAJAYA_QRIS"
                alt="Kode QRIS"
                className="w-44 h-44 mx-auto"
              />
            </div>
            <p className="text-[11px] text-gray-500">
              Buka GoPay, OVO, Dana, atau mobile banking kamu untuk scan.
            </p>
            <div className="flex gap-2">
              <button
                type="button"
                onClick={() => setShowQris(false)}
                className="flex-1 py-2.5 rounded-xl text-xs font-bold border border-gray-300 text-gray-600 hover:bg-gray-50 cursor-pointer"
              >
                Batal
              </button>
              <button
                type="button"
                onClick={confirmOrder}
                className="flex-1 py-2.5 bg-[#165823] text-white rounded-xl text-xs font-bold hover:bg-[#12481c] cursor-pointer"
              >
                Saya sudah bayar
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
