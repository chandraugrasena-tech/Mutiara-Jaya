
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import ProductCard from "../../components/ProductCard";
import { useCart } from "../../utils/CartContext";
import { useProducts } from "../../utils/ProductContext";

export default function Dashboard() {
  const [activeCategory, setActiveCategory] = useState("Semua");
  const [page, setPage] = useState(1);

  const { searchQuery, setSearchQuery } = useCart();
  const { getProducts, getCategories } = useProducts();

  const q = searchQuery.trim();

  // Mengambil daftar kategori dari Laravel API
  const {
    data: categories = [],
    isLoading: categoriesLoading,
  } = useQuery({
    queryKey: ["categories"],
    queryFn: getCategories,
  });

  // Mengambil produk dari Laravel API
  const {
    data: productResponse,
    isLoading,
    isError,
    error,
  } = useQuery({
    queryKey: ["products", "dashboard", page, activeCategory, q],
    queryFn: () =>
      getProducts({
        page,
        category_id: activeCategory === "Semua" ? "" : activeCategory,
        keyword: q,
      }),
  });

  const products = productResponse?.data ?? [];
  const meta = productResponse?.meta;

  // Menyesuaikan format produk API dengan ProductCard
  const formattedProducts = products.map((product) => ({
    ...product,
    img: product.img_url,
    category_name: product.category?.name ?? "",
  }));

  return (
    <div className="space-y-6">
      {/* Banner utama */}
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

      {/* Filter kategori */}
      <div className="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
        <div className="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
          <button
            onClick={() => {
              setActiveCategory("Semua");
              setPage(1);
            }}
            className={`px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm whitespace-nowrap transition-all ${
              activeCategory === "Semua"
                ? "bg-[#165823] text-white shadow-md"
                : "bg-[#fff2f2] text-gray-700 hover:bg-[#ffdada] hover:text-[#165823]"
            }`}
          >
            Semua
          </button>

          {!categoriesLoading &&
            categories.map((category) => (
              <button
                key={category.id}
                onClick={() => {
                  setActiveCategory(String(category.id));
                  setPage(1);
                }}
                className={`px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm whitespace-nowrap transition-all ${
                  activeCategory === String(category.id)
                    ? "bg-[#165823] text-white shadow-md"
                    : "bg-[#fff2f2] text-gray-700 hover:bg-[#ffdada] hover:text-[#165823]"
                }`}
              >
                {category.name}
              </button>
            ))}
        </div>
      </div>

      {/* Daftar produk */}
      <div>
        <h3 className="font-extrabold text-gray-800 text-lg sm:text-xl mb-4">
          {q ? (
            <>Hasil untuk "{searchQuery}"</>
          ) : (
            <>
              Daftar menu:{" "}
              <span className="text-[#165823]">
                {activeCategory === "Semua"
                  ? "Semua"
                  : categories.find(
                      (category) =>
                        String(category.id) === activeCategory
                    )?.name}
              </span>
            </>
          )}
        </h3>

        {isLoading ? (
          <div className="text-center py-12 text-gray-500">
            <p className="text-3xl mb-2">⏳</p>
            <p className="text-sm">Sedang mengambil data menu...</p>
          </div>
        ) : isError ? (
          <div className="text-center py-12 space-y-2">
            <p className="text-3xl">⚠️</p>
            <p className="text-sm text-red-600 font-bold">
              Gagal mengambil data produk.
            </p>
            <p className="text-xs text-gray-500">
              {error?.message || "Periksa koneksi ke server Laravel."}
            </p>
          </div>
        ) : formattedProducts.length === 0 ? (
          <div className="text-center py-12 space-y-3">
            <div className="text-5xl">🍽️</div>
            <p className="text-sm text-gray-600">
              Menu yang kamu cari belum ada.
            </p>

            <button
              onClick={() => {
                setSearchQuery("");
                setActiveCategory("Semua");
                setPage(1);
              }}
              className="text-xs font-bold text-[#165823] underline cursor-pointer"
            >
              Tampilkan semua menu
            </button>
          </div>
        ) : (
          <>
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
              {formattedProducts.map((item) => (
                <ProductCard key={item.id} p={item} />
              ))}
            </div>

            {/* Pagination */}
            {meta && meta.last_page > 1 && (
              <div className="flex justify-center items-center gap-4 mt-8">
                <button
                  onClick={() => setPage((current) => current - 1)}
                  disabled={page <= 1}
                  className="px-4 py-2 rounded-xl bg-[#165823] text-white text-sm font-bold disabled:opacity-40 disabled:cursor-not-allowed"
                >
                  Sebelumnya
                </button>

                <span className="text-sm font-bold text-gray-700">
                  Halaman {meta.current_page} dari {meta.last_page}
                </span>

                <button
                  onClick={() => setPage((current) => current + 1)}
                  disabled={page >= meta.last_page}
                  className="px-4 py-2 rounded-xl bg-[#165823] text-white text-sm font-bold disabled:opacity-40 disabled:cursor-not-allowed"
                >
                  Berikutnya
                </button>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}
