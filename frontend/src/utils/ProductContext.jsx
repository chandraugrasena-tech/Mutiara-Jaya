import React, {
  createContext,
  useContext,
  useCallback,
} from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import apiClient from "./apiClient";

const ProductContext = createContext();

export const ProductProvider = ({ children }) => {
  const queryClient = useQueryClient();

  // Mengambil produk dari API Laravel
  const {
    data: products,
    isLoading,
    isError,
  } = useQuery({
    queryKey: ["products"],
    queryFn: async () => {
      const res = await apiClient.get("/products");
      return res.data.data;
    },
  });

  // Mengambil detail produk berdasarkan ID
  const getProductById = useCallback(async (id) => {
  const res = await apiClient.get(`/products/${id}`);
  const product = res.data.data ?? res.data;

  return {
    ...product,
    category_id: product.category?.id ?? product.category_id,
  };
}, []);

  // Menambahkan produk
  const addProduct = async (formData) => {
    const response = await apiClient.post("/products", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    await queryClient.invalidateQueries({ queryKey: ["products"] });

    return response;
  };

  // Memperbarui produk
  const updateProduct = async (id, formData) => {
    const response = await apiClient.post(
      `/products/${id}?_method=PUT`,
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      }
    );

    await queryClient.invalidateQueries({ queryKey: ["products"] });

    return response;
  };

  // Menghapus produk
  const deleteProduct = useMutation({
    mutationFn: async (id) => {
      return await apiClient.delete(`/products/${id}`);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["products"] });
    },
  });

  // Mengambil kategori dan menyesuaikan formatnya
  // agar tetap cocok dengan ProductForm dan ProductEdit
  const getCategories = async () => {
    const res = await apiClient.get("/categories");

    const categories = res.data.data ?? [];

    return categories.map((cat) => ({
      ...cat,
      category_id: cat.id,
      category: cat.name,
    }));
  };

  // Mengambil daftar produk dengan pencarian dan filter kategori
  const getProducts = async ({
    page = 1,
    limit = 10,
    category_id = "",
    keyword = "",
  } = {}) => {
    const res = await apiClient.get("/products", {
      params: {
        page,
        category_id,
        s: keyword,
      },
    });

    return res.data;
  };

  return (
    <ProductContext.Provider
      value={{
        products,
        isLoading,
        isError,
        getProductById,
        addProduct,
        updateProduct,
        deleteProduct,
        getCategories,
        getProducts,
      }}
    >
      {children}
    </ProductContext.Provider>
  );
};

export const useProducts = () => useContext(ProductContext);