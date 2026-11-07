
import { useQuery } from "@tanstack/react-query";
import apiClient from "../../utils/apiClient";

const formatRupiah = (value) => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value) || 0);
};

export default function About() {
  const {
    data: info,
    isLoading,
    isError,
  } = useQuery({
    queryKey: ["store-info"],
    queryFn: async () => {
      const response = await apiClient.get("/store-info");
      return response.data;
    },
  });

  if (isLoading) {
    return (
      <div className="text-center py-16 text-gray-500">
        Memuat informasi toko...
      </div>
    );
  }

  if (isError || !info) {
    return (
      <div className="max-w-3xl mx-auto bg-white rounded-2xl p-6 text-center">
        <p className="text-red-600 font-semibold">
          Informasi toko gagal dimuat.
        </p>
        <p className="text-sm text-gray-500 mt-2">
          Silakan periksa koneksi ke server Laravel.
        </p>
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto space-y-5">

      {/* Identitas toko */}
      <div className="bg-gradient-to-r from-[#165823] to-[#218335] text-white rounded-3xl p-6">
        <h1 className="text-2xl font-black">
          {info.store_name}
        </h1>
        <p className="text-sm text-green-100 mt-1">
          {info.store_tagline}
        </p>
      </div>

      {/* Tentang kami */}
      <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-2">
        <h2 className="font-bold text-lg text-gray-800">
          Tentang kami
        </h2>
        <p className="text-sm text-gray-600 leading-relaxed">
          {info.store_description}
        </p>
      </div>

      {/* Pengantaran dan informasi toko */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-2 text-sm">
          <h3 className="font-bold text-gray-800">
            Pengantaran
          </h3>

          <p>
            <span className="text-gray-500">Area antar:</span>{" "}
            {info.delivery_area}
          </p>

          <p>
            <span className="text-gray-500">Ongkos kirim:</span>{" "}
            {formatRupiah(info.delivery_fee)}
          </p>

          <p>
            <span className="text-gray-500">Pembayaran:</span>{" "}
            CASH dan QRIS
          </p>
        </div>

        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-2 text-sm">
          <h3 className="font-bold text-gray-800">
            Informasi toko
          </h3>

          <p>
            <span className="text-gray-500">Alamat:</span>{" "}
            {info.store_address || "Belum diisi"}
          </p>

          <p>
            <span className="text-gray-500">Jam buka:</span>{" "}
            {info.store_hours || "Belum diisi"}
          </p>
        </div>
      </div>

      {/* Hubungi kami */}
      <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-2 text-sm">
        <h3 className="font-bold text-gray-800">
          Hubungi kami
        </h3>

        {info.store_phone && (
          <p>
            <span className="text-gray-500">Telepon:</span>{" "}
            {info.store_phone}
          </p>
        )}

        {info.store_whatsapp && (
          <p>
            <span className="text-gray-500">WhatsApp:</span>{" "}
            <a
              href={`https://wa.me/${info.store_whatsapp}`}
              target="_blank"
              rel="noreferrer"
              className="text-[#165823] font-bold underline"
            >
              Chat sekarang
            </a>
          </p>
        )}

        {info.store_email && (
          <p>
            <span className="text-gray-500">Email:</span>{" "}
            {info.store_email}
          </p>
        )}
      </div>
    </div>
  );
}
