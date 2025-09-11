import axios, { AxiosResponse } from 'axios';

interface SearchFilters {
    [key: string]: {
        eq?: any;
        like?: string;
        in?: any[];
        gte?: any;
        gt?: any;
        lte?: any;
        lt?: any;
        between?: any[];
    }
}

interface SearchParams {
    filters?: SearchFilters;
    sort?: string;
    direction?: 'asc' | 'desc';
}

export abstract class ApiService {
    protected baseUrl: string;

    protected constructor(baseUrl: string) {
        this.baseUrl = baseUrl;
    }

    protected async search<T>(params: SearchParams): Promise<T[]> {
        try {
            const response: AxiosResponse<{ data: T[]}> = await axios.post(`${this.baseUrl}/search`, params)
            return response.data.data;// TODO: Handle pagination
        } catch (error) {
            console.error(`Error while searching ${this.baseUrl}:`, error);
            throw error;
        }
    }

    //TODO: index, show, create, update, relationships actions
}
