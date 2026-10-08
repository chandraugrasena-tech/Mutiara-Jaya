import { useState } from "react";
import ProductCard from "../../components/ProductCard";
import { products } from "../../utils/data";
import { useCart } from "../../utils/CartContext";

const categories = ["Semua", "Makanan Utama", "Minuman", "Camilan & Pelengkap"];

export default function Dashboard() {
  const [activeCategory, setActiveCategory] = useState("Semua");
  const { searchQuery, setSearchQuery } = useCart();

  const q = searchQuery.trim().toLowerCase();
  const filteredProducts = products.filter((p) => {
    const matchCategory = activeCategory === "Semua" || p.category_name === activeCategory;
    const matchSearch =
      !q || p.name.toLowerCase().includes(q) || (p.description || "").toLowerCase().includes(q);
    return matchCategory && matchSearch;
  });

  return (
    <div className="space-y-6">
      <div className="bg-gradient-to-r from-[#165823] to-[#218335] text-white p-6 rounded-3xl shadow-sm relative overflow-hidden flex justify-between items-center">
        <div className="z-10 max-w-[70%]">
          <span className="bg-[#ff788d] text-white text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
            Restoran Mutiara Jaya
          </span>
          <h2 className="text-xl sm:text-3xl font-black mt-2 leading-tight">
            Pesan Menu Favoritmu Langsung dari Dapur Kami 🍲
          </h2>
          <p className="text-xs sm:text-sm text-[#ffdada] mt-1">
            Pengiriman cepat, sajian hangat & higienis setiap hari.
          </p>
        </div>
        <div className="text-6xl sm:text-7xl">🏬</div>
      </div>

      <div className="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
        <div className="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
          {categories.map((name) => (
            <button
              key={name}
              onClick={() => setActiveCategory(name)}
              className={`px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm whitespace-nowrap transition-all cursor-pointer ${
                activeCategory === name
                  ? "bg-[#165823] text-white shadow-md"
                  : "bg-[#fff2f2] text-gray-700 hover:bg-[#ffdada] hover:text-[#165823]"
              }`}
            >
              {name}
            </button>
          ))}
        </div>
      </div>

      <div>
        <h3 className="font-extrabold text-gray-800 text-lg sm:text-xl mb-4">
          {q ? (
            <>Hasil untuk "{searchQuery}"</>
          ) : (
            <>
              Daftar menu: <span className="text-[#165823]">{activeCategory}</span>
            </>
          )}
        </h3>

        {filteredProducts.length === 0 ? (
          <div className="text-center py-12 space-y-3">
            <div className="text-5xl">🍽️</div>
            <p className="text-sm text-gray-600">Menu yang kamu cari belum ada.</p>
            <button
              onClick={() => {
                setSearchQuery("");
                setActiveCategory("Semua");
              }}
              className="text-xs font-bold text-[#165823] underline cursor-pointer"
            >
              Tampilkan semua menu
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            {filteredProducts.map((item) => (
              <ProductCard key={item.id} p={item} />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
