import { AxiosInstance, AxiosResponse } from "axios";

export default class HttpResource {

    constructor(
        protected http: AxiosInstance,
        protected resource: any
    ) {

    }

    list<T = any>(): Promise<AxiosResponse<T>> {
        return this.http.get<T>(this.resource);
    }

    get<T = any>(id: string | number): Promise<AxiosResponse<T>> {
        return this.http.get<T>(`${this.resource}/${id}`);
    }

    create<T = any>(data: any): Promise<AxiosResponse<T>> {
        return this.http.post<T>(this.resource, data);
    }

    update<T = any>(id: string | number, data: any): Promise<AxiosResponse<T>> {
        return this.http.put<T>(`${this.resource}/${id}`, data);
    }

    delete<T = any>(id: string | number): Promise<AxiosResponse<T>> {
        return this.http.delete<T>(`${this.resource}/${id}`);
    }
}