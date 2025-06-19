import 'package:app/models/user.dart';
import 'package:app/services/search_api.dart';
import 'package:flutter/material.dart';

class SearchProvider with ChangeNotifier {
  final SearchApi _apiService = SearchApi();

  List<User> _results = [];
  List<User> get results => _results;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _error;
  String? get error => _error;

  Future<void> searchUsers({
    String? query,
    String? role,
    double? radius,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      _results = await _apiService.searchUsers(
        query: query,
        role: role,
        radius: radius,
      );
    } catch (e) {
      _error = e.toString();
    }

    _isLoading = false;
    notifyListeners();
  }

  void clearResults() {
    _results = [];
    notifyListeners();
  }
}
