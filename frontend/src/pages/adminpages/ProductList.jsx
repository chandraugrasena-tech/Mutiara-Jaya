
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { useProducts } from "../../utils/ProductContext";
import toast from "react-hot-toast";

export default function ProductList() {
  const { getProducts, deleteProduct } = useProducts();
  const [page, setPage] = useState(1);

  const {
    data: productResponse,
    isLoading,
    isError,
  } = useQuery({
    queryKey: ["products", "admin", page],
    queryFn: () => getProducts({ page }),
  });

  const products = productResponse?.data ?? [];
  const currentPage = productResponse?.meta?.current_page ?? page;
  const lastPage = productResponse?.meta?.last_page ?? 1;

  const handleDelete = async (id) => {
    const confirmDelete = window.confirm(
      "Apakah kamu yakin ingin menghapus produk ini?"
    );

    if (!confirmDelete) return;

    try {
      await deleteProduct.mutateAsync(id);
      toast.success("Produk berhasil dihapus");
    } catch (error) {
      toast.error(
        error.response?.data?.message || "Gagal menghapus produk"
      );
    }
  };

  if (isLoading) {
    return <p className="text-gray-600">Memuat data produk...</p>;
  }

  if (isError) {
    return <p className="text-red-600">Gagal mengambil data produk.</p>;
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-bold">Daftar Produk</h1>

        <Link
          to="/admin/add-product"
          className="bg-[#165823] text-white px-4 py-2 rounded-lg hover:bg-green-800"
        >
          + Tambah Produk
        </Link>
      </div>

      <div className="bg-white rounded-lg shadow overflow-x-auto">
        <table className="w-full text-sm text-left">
          <thead className="bg-gray-100 text-gray-700">
            <tr>
              <th className="p-4">Gambar</th>
              <th className="p-4">Nama Produk</th>
              <th className="p-4">Kategori</th>
              <th className="p-4">Harga</th>
              <th className="p-4">Stok</th>
              <th className="p-4">Aksi</th>
            </tr>
          </thead>

          <tbody>
            {products.length === 0 ? (
              <tr>
                <td colSpan="6" className="p-6 text-center text-gray-500">
                  Belum ada produk.
                </td>
              </tr>
            ) : (
              products.map((product) => (
                <tr key={product.id} className="border-t">
                  <td className="p-4">
                    {product.img_url ? (
                      <img
                        src={product.img_url}
                        alt={product.name}
                        className="w-14 h-14 object-cover rounded"
                      />
                    ) : (
                      <span className="text-gray-400">Tidak ada gambar</span>
                    )}
                  </td>

                  <td className="p-4 font-medium">{product.name}</td>

                  <td className="p-4">
                    {product.category?.name || "-"}
                  </td>

                  <td className="p-4">
                    Rp{Number(product.price).toLocaleString("id-ID")}
                  </td>

                  <td className="p-4">{product.stock}</td>

                  <td className="p-4">
                    <div className="flex gap-2">
                      <Link
                        to={`/admin/edit-product/${product.id}`}
                        className="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
                      >
                        Edit
                      </Link>

                      <button
                        onClick={() => handleDelete(product.id)}
                        disabled={deleteProduct.isPending}
                        className="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 disabled:opacity-50"
                      >
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {/* Navigasi halaman */}
      <div className="flex items-center justify-between gap-3">
        <p className="text-sm text-gray-600">
          Halaman {currentPage} dari {lastPage}
        </p>

        <div className="flex gap-2">
          <button
            onClick={() => setPage((prev) => prev - 1)}
            disabled={currentPage <= 1}
            className="px-4 py-2 border rounded-lg disabled:opacity-40"
          >
            Sebelumnya
          </button>

          <button
            onClick={() => setPage((prev) => prev + 1)}
            disabled={currentPage >= lastPage}
            className="px-4 py-2 border rounded-lg disabled:opacity-40"
          >
            Berikutnya
          </button>
        </div>
      </div>
    </div>
  );
}
