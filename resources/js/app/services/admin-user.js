import { httpClient } from '../helpers/http-client';
import { apiConstants } from '../helpers/api-constants';

export const getPaginatedList = async () => {
    const url = apiConstants.adminUsers;
    return httpClient.get(url).then(res => res.data);
}
