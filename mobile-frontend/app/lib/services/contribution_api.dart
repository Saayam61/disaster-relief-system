import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/contribution.dart';

class ContributionApi {
  final String baseUrl = 'http://localhost:8000/api';
  Future<List<Contribution>> getUserContributions() async {
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('login_token');
    final response = await http.get(
      Uri.parse('$baseUrl/contributions/user'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    if (response.statusCode == 200) {
      final List<dynamic> data = json.decode(response.body)['data'];
      return data.map((json) => Contribution.fromJson(json)).toList();
    } else {
      throw Exception('Failed to load contributions');
    }
  }

  Future<List<Contribution>> getVolContributions({required int userId}) async {
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('login_token');
    final response = await http.get(
      Uri.parse('$baseUrl/contributions/user'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    if (response.statusCode == 200) {
      final List<dynamic> data = json.decode(response.body)['data'];
      return data.map((json) => Contribution.fromJson(json)).toList();
    } else {
      throw Exception('Failed to load contributions');
    }
  }

  Future<void> deleteContribution(String id) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final response = await http.delete(
      Uri.parse('$baseUrl/contributions/$id'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    if (response.statusCode != 200 && response.statusCode != 204) {
      throw Exception('Failed to delete contribution');
    }
  }
}
