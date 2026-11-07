
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { useCart } from "../../utils/CartContext";
import { useProducts } from "../../utils/ProductContext";
import { products } from "../../utils/data";
import { rp } from "../../utils/promos";

export default function AdminDashboard() {
  const { orders } = useCart();
  const { getProducts, getCategories } = useProducts();

  // State pencarian dan filter
  const [keywordInput, setKeywordInput] = useState("");
  const [categoryInput, setCategoryInput] = useState("");

  const [filters, setFilters] = useState({
    keyword: "",
    category_id: "",
  });

  // State pagination
  const [page, setPage] = useState(1);

  // Mengambil kategori dari Laravel
  const {
    data: categories = [],
    isError: categoriesError,
  } = useQuery({
    queryKey: ["categories"],
    queryFn: getCategories,
  });

  // Mengambil produk dari Laravel
  const {
    data: productResponse,
    isLoading: productsLoading,
    isError: productsError,
  } = useQuery({
    queryKey: ["products", "dashboard-search", page, filters],
    queryFn: () =>
      getProducts({
        page,
        category_id: filters.category_id,
        keyword: filters.keyword,
      }),
  });

  const filteredProducts = productResponse?.data ?? [];
  const currentPage = productResponse?.meta?.current_page ?? page;
  const lastPage = productResponse?.meta?.last_page ?? 1;

  // Menjalankan pencarian
  const handleSearch = (e) => {
    e.preventDefault();

    setPage(1);
    setFilters({
      keyword: keywordInput.trim(),
      category_id: categoryInput,
    });
  };

  // Menghapus pencarian dan filter
  const handleReset = () => {
    setKeywordInput("");
    setCategoryInput("");

    setFilters({
      keyword: "",
      category_id: "",
    });

    setPage(1);
  };

  // Statistik dashboard
  const totalPendapatan = orders.reduce(
    (sum, o) => sum + o.total,
    0
  );

  const stats = [
    {
      icon: "",
      label: "Jumlah Pesanan",
      value: orders.length,
    },
    {
      icon: "",
      label: "Total Pendapatan",
      value: rp(totalPendapatan),
    },
    {
      icon: "",
      label: "Jumlah Menu",
      value: products.length,
    },
  ];

  const getStatus = (order) =>
    Date.now() - order.createdAt > 24000
      ? "Selesai"
      : "Diproses";

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Dashboard</h1>

      {/* Statistik Dashboard */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {stats.map((s) => (
          <div
            key={s.label}
            className="bg-white p-5 rounded shadow flex items-center gap-4"
          >
            <div className="text-3xl">{s.icon}</div>

            <div>
              <p className="text-xs text-gray-500">
                {s.label}
              </p>

              <p className="text-xl font-extrabold text-gray-800">
                {s.value}
              </p>
            </div>
          </div>
        ))}
      </div>

      {/* Pencarian dan Filter Produk */}
      <div className="bg-white p-5 rounded shadow space-y-4">
        <h2 className="font-bold text-lg">
          Cari & Filter Produk
        </h2>

        <form
          onSubmit={handleSearch}
          className="flex flex-col md:flex-row gap-3"
        >
          {/* Input pencarian */}
          <input
            type="text"
            value={keywordInput}
            onChange={(e) => setKeywordInput(e.target.value)}
            placeholder="Cari nama produk..."
            className="border rounded-lg px-4 py-2 flex-1 outline-none focus:ring-2 focus:ring-green-600"
          />

          {/* Dropdown kategori */}
          <select
            value={categoryInput}
            onChange={(e) => setCategoryInput(e.target.value)}
            className="border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-green-600"
          >
            <option value="">
              Semua Kategori
            </option>

            {categories.map((category) => (
              <option
                key={category.category_id}
                value={category.category_id}
              >
                {category.category}
              </option>
            ))}
          </select>

          {/* Tombol cari */}
          <button
            type="submit"
            className="bg-[#165823] text-white px-5 py-2 rounded-lg hover:bg-green-800"
          >
            Cari
          </button>

          {/* Tombol reset */}
          <button
            type="button"
            onClick={handleReset}
            className="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300"
          >
            Reset
          </button>
        </form>

        {categoriesError && (
          <p className="text-sm text-red-600">
            Gagal mengambil kategori dari server.
          </p>
        )}

        {/* Hasil pencarian */}
        {productsLoading ? (
          <p className="text-sm text-gray-500 py-4">
            Memuat produk...
          </p>
        ) : productsError ? (
          <p className="text-sm text-red-600 py-4">
            Gagal mengambil data produk. Periksa koneksi ke server Laravel.
          </p>
        ) : (
          <>
            <div className="overflow-x-auto">
              <table className="w-full text-sm text-left">
                <thead>
                  <tr className="border-b text-gray-600">
                    <th className="py-3 pr-4">
                      Nama Produk
                    </th>

                    <th className="py-3 pr-4">
                      Kategori
                    </th>

                    <th className="py-3 pr-4">
                      Harga
                    </th>

                    <th className="py-3">
                      Stok
                    </th>
                  </tr>
                </thead>

                <tbody>
                  {filteredProducts.length === 0 ? (
                    <tr>
                      <td
                        colSpan="4"
                        className="py-6 text-center text-gray-500"
                      >
                        Produk tidak ditemukan.
                      </td>
                    </tr>
                  ) : (
                    filteredProducts.map((product) => (
                      <tr
                        key={product.id}
                        className="border-b last:border-0"
                      >
                        <td className="py-3 pr-4 font-medium">
                          {product.name}
                        </td>

                        <td className="py-3 pr-4">
                          {product.category?.name ?? "-"}
                        </td>

                        <td className="py-3 pr-4">
                          {rp(product.price)}
                        </td>

                        <td className="py-3">
                          {product.stock ?? 0}
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            <div className="flex items-center justify-between pt-3">
              <button
                type="button"
                onClick={() =>
                  setPage((oldPage) =>
                    Math.max(oldPage - 1, 1)
                  )
                }
                disabled={currentPage <= 1}
                className="px-4 py-2 rounded bg-gray-200 disabled:opacity-40"
              >
                Sebelumnya
              </button>

              <span className="text-sm text-gray-600">
                Halaman {currentPage} dari {lastPage}
              </span>

              <button
                type="button"
                onClick={() =>
                  setPage((oldPage) =>
                    Math.min(oldPage + 1, lastPage)
                  )
                }
                disabled={currentPage >= lastPage}
                className="px-4 py-2 rounded bg-gray-200 disabled:opacity-40"
              >
                Berikutnya
              </button>
            </div>
          </>
        )}
      </div>

      {/* Daftar Pesanan Masuk */}
      <div className="bg-white p-5 rounded shadow">
        <h2 className="font-bold mb-3">
          Daftar Pesanan Masuk
        </h2>

        {orders.length === 0 ? (
          <div className="text-sm text-gray-500 py-6 text-center">
            Belum ada pesanan. Buat pesanan dari halaman toko, nanti muncul di sini.

            <div className="mt-3">
              <Link
                to="/"
                className="font-bold text-[#165823] underline"
              >
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
                  <tr
                    key={o.id}
                    className="border-b last:border-0"
                  >
                    <td className="py-2 pr-4 font-mono font-bold">
                      {o.id}
                    </td>

                    <td className="py-2 pr-4 whitespace-nowrap">
                      {new Date(o.createdAt).toLocaleString("id-ID")}
                    </td>

                    <td className="py-2 pr-4">
                      {o.address ? o.address.label : "-"}
                    </td>

                    <td className="py-2 pr-4">
                      {o.items.reduce(
                        (sum, item) => sum + item.quantity,
                        0
                      )}{" "}
                      porsi
                    </td>

                    <td className="py-2 pr-4">
                      {o.paymentMethod}
                    </td>

                    <td className="py-2 pr-4 font-bold">
                      {rp(o.total)}
                    </td>

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
