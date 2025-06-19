import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:app/models/user.dart';
import 'package:shared_preferences/shared_preferences.dart';
class SearchApi {
  final String baseUrl = 'http://localhost:8000/api';

  Future<List<User>> searchUsers({
    String? query,
    String? role,
    double? radius,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final Map<String, String> headers = {
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    };

    final Map<String, String> params = {};

    if (query != null) params['query'] = query;
    if (role != null) params['role'] = role;
    if (radius != null) params['radius'] = radius.toString();

    final uri = Uri.parse('$baseUrl/search').replace(queryParameters: params);

    final response = await http.get(uri, headers: headers);

  print(response.body); // Debugging line to check the response body
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      final List usersJson = data['data'];
      return usersJson.map((json) => User.fromJson(json)).toList();
    } else {
      throw Exception('Failed to load search results');
    }
  }
}
