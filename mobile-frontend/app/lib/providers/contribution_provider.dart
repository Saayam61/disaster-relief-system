import 'package:flutter/material.dart';
import '../models/contribution.dart';
import '../services/contribution_api.dart';

class ContributionProvider extends ChangeNotifier {
  List<Contribution> _contributions = [];
  List<Contribution> get contributions => _contributions;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  ContributionApi _contributionApi = ContributionApi();
  Future<void> getUserContributions() async {
    _isLoading = true;
    notifyListeners();
    try {
      final fetched = await _contributionApi.getUserContributions();
      _contributions = fetched;
    } catch (e, stacktrace) {
      print('Error fetching contributions: $e');
      print(stacktrace);
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<void> getVolContributions() async {
    _isLoading = true;
    notifyListeners();
    try {
      // final fetched = await _contributionApi.getVolContributions();
      // _contributions = fetched;
    } catch (e, stacktrace) {
      print('Error fetching contributions: $e');
      print(stacktrace);
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<void> deleteContribution(String id) async {
    try {
      await _contributionApi.deleteContribution(id);
      _contributions.removeWhere((c) => c.id == id);
      notifyListeners();
    } catch (e) {
      print('Error deleting contribution: $e');
    }
  }
}
