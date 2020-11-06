import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/players',
    method: 'get',
    params: query,
  });
}

export function fetchPlayer(id) {
  return request({
    url: '/players/' + id,
    method: 'get',
  });
}

export function createPlayer(data) {
  return request({
    url: '/players',
    method: 'post',
    data,
  });
}

export function updatePlayer(data) {
  return request({
    url: '/players/' + data.id,
    method: 'put',
    data,
  });
}

export function deletePlayer(data) {
  return request({
    url: '/players/' + data.id,
    method: 'delete',
  });
}
