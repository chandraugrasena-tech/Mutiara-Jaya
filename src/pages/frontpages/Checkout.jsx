import { useCart } from "../../utils/CartContext";
import { useNavigate } from "react-router-dom";

export default function Checkout() {
  const { cart, totalQty } = useCart();
  const navigate = useNavigate();
  const totalPrice = cart.reduce((sum, item) => sum + item.price * item.qty, 0);

  const handleCheckout = (e) => {
    e.preventDefault();
    alert("Hore! Pesanan Anda di Mutiara Jaya sedang diproses. Mohon tunggu dengan sabar ya.");
    navigate("/");
  };

  if (cart.length === 0) {
    navigate("/cart");
    return null;
  }

  return (
    <div className="max-w-5xl mx-auto flex flex-col md:flex-row gap-8">
      <div className="w-full md:w-2/3 bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100">
        <h2 className="text-2xl font-extrabold text-[#165823] mb-6">Detail Pengiriman</h2>
        <form onSubmit={handleCheckout} className="space-y-4">
          <div>
            <label className="block text-gray-700 font-bold mb-2">Nama Penerima</label>
            <input required type="text" className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none" placeholder="Masukkan nama kamu" />
          </div>
          <div>
            <label className="block text-gray-700 font-bold mb-2">Alamat Lengkap</label>
            <textarea required rows="3" className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none" placeholder="Alamat pengiriman makanan"></textarea>
          </div>
          <div>
            <label className="block text-gray-700 font-bold mb-2">Catatan Pesanan (Opsional)</label>
            <input type="text" className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none" placeholder="Contoh: Sambal dipisah ya" />
          </div>
          
          <button type="submit" className="w-full mt-6 bg-[#ff788d] text-white py-4 rounded-xl font-bold text-lg hover:bg-[#ff5f78] hover:shadow-lg transition-all">
            Pesan Sekarang
          </button>
        </form>
      </div>

      <div className="w-full md:w-1/3 bg-[#ffdada] p-6 rounded-3xl shadow-sm h-fit">
        <h3 className="text-xl font-bold text-[#165823] mb-4">Ringkasan Pesanan</h3>
        <p className="text-gray-700 font-medium mb-4">Total Item: {totalQty} porsi</p>
        <div className="border-t border-white pt-4 mt-4">
          <div className="flex justify-between items-center mb-2">
            <span className="text-gray-700">Subtotal</span>
            <span className="font-bold">Rp {totalPrice.toLocaleString('id-ID')}</span>
          </div>
          <div className="flex justify-between items-center mb-4">
            <span className="text-gray-700">Ongkir</span>
            <span className="font-bold">Rp 10.000</span>
          </div>
          <div className="flex justify-between items-center border-t border-white pt-4">
            <span className="text-lg font-extrabold text-[#165823]">Total</span>
            <span className="text-xl font-extrabold text-[#165823]">
              Rp {(totalPrice + 10000).toLocaleString('id-ID')}
            </span>
          </div>
        </div>
      </div>
    </div>
  );
}