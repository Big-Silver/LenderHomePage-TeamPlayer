import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/teams',
    method: 'get',
    params: query,
  });
}

export function fetchTeam(id) {
  return request({
    url: '/teams/' + id,
    method: 'get',
  });
}

export function createTeam(data) {
  return request({
    url: '/teams',
    method: 'post',
    data,
  });
}

export function updateteam(data) {
  return request({
    url: '/teams/' + data.id,
    method: 'put',
    data,
  });
}

export function deleteTeam(data) {
  return request({
    url: '/teams/' + data.id,
    method: 'delete',
  });
}
