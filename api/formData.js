/**
 * 將 Object 轉換為 FormData
 * 支援嵌套物件、陣列以及多檔案
 */
export function toFormData(object) {
  const formData = new FormData();

  Object.keys(object).forEach((key) => {
    const value = object[key];

    if (Array.isArray(value)) {
      // 如果是陣列 (例如多個檔案或多個 ID)
      value.forEach((item, index) => {
        // PHP 習慣用 key[] 來接收陣列
        const arrayKey = `${key}[]`;
        formData.append(arrayKey, item);
      });
    } else if (value instanceof File) {
      // 如果是單一檔案
      formData.append(key, value);
    } else if (value !== null && value !== undefined) {
      // 普通文字或數值
      formData.append(key, value);
    }
  });

  return formData;
}
