import { useState } from "react";
import { useParams, useNavigate, Link } from "react-router-dom";
import { products } from "../../utils/data";
import { useCart } from "../../utils/CartContext";
import { rp } from "../../utils/promos";

export default function ProductDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { addToCart } = useCart();
  const [qty, setQty] = useState(1);
  const [added, setAdded] = useState(false);

  const product = products.find((p) => String(p.id) === id);

  if (!product) {
    return (
      <div className="text-center py-20 text-[#165823]">
        <h2 className="text-2xl font-bold">Menu tidak ditemukan</h2>
        <button onClick={() => navigate("/")} className="mt-4 underline cursor-pointer">
          Kembali ke beranda
        </button>
      </div>
    );
  }

  const handleAdd = () => {
    addToCart(product, qty);
    setAdded(true);
    setQty(1);
    setTimeout(() => setAdded(false), 1500);
  };

  return (
    <div className="bg-white rounded-3xl p-6 md:p-10 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-8 max-w-4xl mx-auto">
      <div className="w-full md:w-1/2">
        <img
          src={product.img}
          alt={product.name}
          className="w-full h-64 md:h-96 object-cover rounded-2xl shadow-sm"
        />
      </div>

      <div className="w-full md:w-1/2 flex flex-col justify-center">
        <span className="inline-block bg-[#ffdada] text-[#165823] px-3 py-1 rounded-lg text-sm font-bold w-fit mb-4">
          {product.category_name}
        </span>
        <h1 className="text-3xl md:text-4xl font-extrabold text-[#165823] mb-2">{product.name}</h1>
        <p className="text-[#ff788d] text-2xl font-extrabold mb-6">{rp(product.price)}</p>

        <div className="mb-6">
          <h3 className="font-bold text-gray-800 mb-2">Deskripsi menu</h3>
          <p className="text-gray-600 leading-relaxed">
            {product.description ||
              "Hidangan spesial dari Mutiara Jaya yang disiapkan dengan bahan berkualitas tinggi dan bumbu pilihan."}
          </p>
        </div>

        <div className="flex items-center gap-3 mb-4">
          <span className="text-sm font-bold text-gray-700">Jumlah</span>
          <div className="flex items-center gap-3 border rounded-xl px-2 py-1 bg-gray-50">
            <button
              type="button"
              onClick={() => setQty((q) => Math.max(1, q - 1))}
              className="w-8 h-8 rounded-lg bg-white border font-bold hover:bg-gray-100 cursor-pointer"
              aria-label="Kurangi"
            >
              -
            </button>
            <span className="font-bold w-5 text-center">{qty}</span>
            <button
              type="button"
              onClick={() => setQty((q) => q + 1)}
              className="w-8 h-8 rounded-lg bg-white border font-bold hover:bg-gray-100 cursor-pointer"
              aria-label="Tambah"
            >
              +
            </button>
          </div>
        </div>

        <div className="flex gap-3 mt-auto">
          <button
            onClick={() => navigate(-1)}
            className="px-5 py-3 border-2 border-[#165823] text-[#165823] font-bold rounded-xl hover:bg-gray-50 transition-colors cursor-pointer"
          >
            Kembali
          </button>
          <button
            onClick={handleAdd}
            className={`flex-1 text-white py-3 rounded-xl font-bold hover:shadow-lg transition-all ${
              added ? "bg-emerald-700" : "bg-[#ff788d] hover:bg-[#ff5f78]"
            }`}
          >
            {added ? "✓ Masuk keranjang" : `+ Keranjang • ${rp(product.price * qty)}`}
          </button>
        </div>

        {added && (
          <Link to="/cart" className="mt-3 text-center text-sm font-bold text-[#165823] underline">
            Lihat keranjang
          </Link>
        )}
      </div>
    </div>
  );
}
